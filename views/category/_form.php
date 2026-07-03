<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var app\models\Category $model */
$form = ActiveForm::begin();
?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<div class="form-group mt-3">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>
