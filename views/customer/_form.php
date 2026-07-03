<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
$form = ActiveForm::begin();
?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>
<?= $form->field($model, 'phone')->textInput(['maxlength' => 30]) ?>
<?= $form->field($model, 'email')->textInput(['maxlength' => 128]) ?>
<?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>
<div class="form-group mt-3">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>
