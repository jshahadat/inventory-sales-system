<?php

namespace app\controllers;

use Yii;
use app\models\SalesInvoice;
use app\models\SalesInvoiceItem;
use app\models\SalesInvoiceSearch;
use app\models\Product;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SalesInvoiceController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['index', 'view', 'pdf'], 'roles' => ['viewInvoice']],
                    ['allow' => true, 'actions' => ['create', 'update', 'delete'], 'roles' => ['manageInvoice']],
                ],
            ],
            'verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new SalesInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    /**
     * Create invoice + dynamic line items in one atomic DB transaction.
     * Expects POST:
     *   SalesInvoice[customer_id], SalesInvoice[invoice_date], SalesInvoice[discount], SalesInvoice[tax]
     *   items[<n>][product_id], items[<n>][qty]
     */
    public function actionCreate()
    {
        $model = new SalesInvoice();
        $model->invoice_no = SalesInvoice::generateInvoiceNo();
        $model->invoice_date = date('Y-m-d');
        $model->created_by = Yii::$app->user->id;
        $model->status = 'due';

        $products = Product::find()->where(['status' => 'active'])->all();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->load($post);
            $itemsPost = $post['items'] ?? [];

            if (empty($itemsPost)) {
                Yii::$app->session->setFlash('error', 'Please add at least one line item.');
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        throw new \Exception('Invoice header validation failed.');
                    }

                    $subTotal = 0;
                    foreach ($itemsPost as $row) {
                        if (empty($row['product_id']) || empty($row['qty'])) {
                            continue;
                        }
                        $product = Product::findOne($row['product_id']);
                        if (!$product) {
                            continue;
                        }
                        if ($product->stock_qty < (int) $row['qty']) {
                            throw new \Exception("Insufficient stock for {$product->name}.");
                        }

                        $item = new SalesInvoiceItem();
                        $item->invoice_id = $model->id;
                        $item->product_id = $product->id;
                        $item->qty = (int) $row['qty'];
                        $item->unit_price = $product->unit_price;
                        if (!$item->save()) {
                            throw new \Exception('Line item save failed: ' . json_encode($item->errors));
                        }

                        // decrement stock
                        $product->stock_qty -= $item->qty;
                        $product->save(false, ['stock_qty']);

                        $subTotal += $item->line_total;
                    }

                    $model->sub_total = $subTotal;
                    $model->grand_total = $subTotal - $model->discount + $model->tax;
                    $model->save(false, ['sub_total', 'grand_total']);

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Invoice created successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Failed: ' . $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'products' => $products,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // restore stock before deleting
            foreach ($model->items as $item) {
                $item->product->updateCounters(['stock_qty' => $item->qty]);
            }
            $model->delete(); // items cascade-delete via FK ON DELETE CASCADE
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Invoice deleted and stock restored.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Delete failed: ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Export invoice as PDF using mPDF library.
     * URL: /sales-invoice/<id>/pdf
     */
    public function actionPdf($id)
    {
        $model = $this->findModel($id);

        $html = $this->renderPartial('pdf', ['model' => $model]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        $mpdf->SetTitle('Invoice ' . $model->invoice_no);
        $mpdf->WriteHTML($html);

        return Yii::$app->response->sendContentAsFile(
            $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN),
            'invoice-' . $model->invoice_no . '.pdf',
            ['mimeType' => 'application/pdf', 'inline' => true]
        );
    }

    protected function findModel($id)
    {
        if (($model = SalesInvoice::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested invoice does not exist.');
    }
}
