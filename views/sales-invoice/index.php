<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var app\models\SalesInvoiceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sales Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-invoice-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('manageInvoice')): ?>
        <p><?= Html::a('+ New Invoice', ['create'], ['class' => 'btn btn-success mb-3']) ?></p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'invoice_no',
            [
                'attribute' => 'customer_name',
                'value' => 'customer.name',
            ],
            'invoice_date',
            [
                'attribute' => 'grand_total',
                'value' => fn($m) => number_format($m->grand_total, 2),
            ],
            [
                'attribute' => 'status',
                'filter' => ['draft' => 'Draft', 'paid' => 'Paid', 'due' => 'Due', 'cancelled' => 'Cancelled'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {pdf} {delete}',
                'buttons' => [
                    'pdf' => fn($url, $model) => Html::a('<span class="bi">PDF</span>', ['pdf', 'id' => $model->id], [
                        'target' => '_blank', 'title' => 'Download PDF',
                    ]),
                ],
                'visibleButtons' => [
                    'delete' => Yii::$app->user->can('manageInvoice'),
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
