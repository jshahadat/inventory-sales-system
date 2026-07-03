<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php if (Yii::$app->user->can('manageProduct')): ?>
<p>
<?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<?= Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger', 'data' => ['confirm' => 'Delete this category?', 'method' => 'post']]) ?>
</p>
<?php endif; ?>
<?= DetailView::widget(['model' => $model, 'attributes' => ['name', 'description']]) ?>
