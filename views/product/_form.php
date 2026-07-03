<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Category;
use app\models\Supplier;

/** @var app\models\Product $model */
?>
<div class="product-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map(Category::find()->all(), 'id', 'name'),
        ['prompt' => 'Select Category']
    ) ?>

    <?= $form->field($model, 'supplier_id')->dropDownList(
        ArrayHelper::map(Supplier::find()->all(), 'id', 'name'),
        ['prompt' => 'Select Supplier']
    ) ?>

    <?= $form->field($model, 'unit_price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
    <?= $form->field($model, 'stock_qty')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'reorder_level')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'status')->dropDownList(['active' => 'Active', 'inactive' => 'Inactive']) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
