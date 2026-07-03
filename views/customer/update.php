<?php
use yii\helpers\Html;
$this->title = 'Update ' . ucfirst('customer') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => ucfirst('customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
