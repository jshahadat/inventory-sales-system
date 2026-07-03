<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\Product;
use app\models\SalesInvoice;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'error'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['index', 'logout'], 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['logout' => ['POST']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $lowStock = Product::find()
            ->where('stock_qty <= reorder_level')
            ->andWhere(['status' => 'active'])
            ->all();

        $recentInvoices = SalesInvoice::find()
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->all();

        $todaySalesTotal = SalesInvoice::find()
            ->where(['invoice_date' => date('Y-m-d')])
            ->sum('grand_total');

        return $this->render('index', [
            'lowStock' => $lowStock,
            'recentInvoices' => $recentInvoices,
            'todaySalesTotal' => $todaySalesTotal ?: 0,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }
}
