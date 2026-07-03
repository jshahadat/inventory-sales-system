<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var app\models\LoginForm $model */

$this->title = 'Login';
?>
<div class="site-login" style="max-width: 400px; margin: 60px auto;">
    <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
