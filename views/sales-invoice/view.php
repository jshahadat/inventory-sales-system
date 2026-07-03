<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var app\models\SalesInvoice $model */

$this->title = $model->invoice_no;
$this->params['breadcrumbs'][] = ['label' => 'Sales Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Download PDF', ['pdf', 'id' => $model->id], ['class' => 'btn btn-outline-primary', 'target' => '_blank']) ?>
    <?php if (Yii::$app->user->can('manageInvoice')): ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Delete this invoice? Stock will be restored.', 'method' => 'post'],
        ]) ?>
    <?php endif; ?>
</p>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'invoice_no',
        'customer.name',
        'creator.username',
        'invoice_date',
        [
            'attribute' => 'sub_total',
            'value' => number_format($model->sub_total, 2),
        ],
        [
            'attribute' => 'discount',
            'value' => number_format($model->discount, 2),
        ],
        [
            'attribute' => 'tax',
            'value' => number_format($model->tax, 2),
        ],
        [
            'attribute' => 'grand_total',
            'value' => number_format($model->grand_total, 2),
        ],
        'status',
    ],
]) ?>

<h4 class="mt-4">Line Items</h4>
<table class="table table-bordered">
    <thead>
        <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr>
    </thead>
    <tbody>
    <?php foreach ($model->items as $item): ?>
        <tr>
            <td><?= Html::encode($item->product->name) ?></td>
            <td><?= $item->qty ?></td>
            <td><?= number_format($item->unit_price, 2) ?></td>
            <td><?= number_format($item->line_total, 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
