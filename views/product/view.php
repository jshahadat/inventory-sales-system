<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var app\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
<?php if (Yii::$app->user->can('manageProduct')): ?>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => ['confirm' => 'Are you sure you want to delete this product?', 'method' => 'post'],
    ]) ?>
<?php endif; ?>
</p>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'sku',
        'name',
        'category.name',
        'supplier.name',
        [
            'attribute' => 'unit_price',
            'value' => number_format($model->unit_price, 2),
        ],
        'stock_qty',
        'reorder_level',
        'status',
    ],
]) ?>
