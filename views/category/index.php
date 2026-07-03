<?php
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php if (Yii::$app->user->can('manageProduct')): ?>
    <p><?= Html::a('+ New Category', ['create'], ['class' => 'btn btn-success mb-3']) ?></p>
<?php endif; ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'name',
        'description',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]) ?>
