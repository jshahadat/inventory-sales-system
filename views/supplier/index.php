<?php
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = ucfirst('suppliers');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::a('+ New', ['create'], ['class' => 'btn btn-success mb-3']) ?></p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'name',
        'phone',
        'email',
        'address',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]) ?>
