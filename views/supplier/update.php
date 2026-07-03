<?php
use yii\helpers\Html;
$this->title = 'Update ' . ucfirst('supplier') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => ucfirst('suppliers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
