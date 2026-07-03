<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('manageProduct')): ?>
        <p><?= Html::a('+ New Product', ['create'], ['class' => 'btn btn-success mb-3']) ?></p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'sku',
            'name',
            [
                'attribute' => 'category_name',
                'value' => 'category.name',
                'filter' => \yii\helpers\ArrayHelper::map(app\models\Category::find()->asArray()->all(), 'name', 'name'),
            ],
            [
                'attribute' => 'supplier_name',
                'value' => 'supplier.name',
                'filter' => \yii\helpers\ArrayHelper::map(app\models\Supplier::find()->asArray()->all(), 'name', 'name'),
            ],
            [
                'attribute' => 'unit_price',
                'value' => fn($m) => number_format($m->unit_price, 2),
            ],
            [
                'attribute' => 'stock_qty',
                'value' => fn($m) => $m->stock_qty,
                'contentOptions' => fn($m) => ['class' => $m->isLowStock ? 'table-danger' : ''],
            ],
            [
                'attribute' => 'status',
                'filter' => ['active' => 'Active', 'inactive' => 'Inactive'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update' => Yii::$app->user->can('manageProduct'),
                    'delete' => Yii::$app->user->can('manageProduct'),
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
