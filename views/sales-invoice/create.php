<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\Customer;

/** @var app\models\SalesInvoice $model */
/** @var app\models\Product[] $products */

$this->title = 'New Sales Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Sales Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$productMap = ArrayHelper::map($products, 'id', 'name');
$productPrices = ArrayHelper::map($products, 'id', 'unit_price');
$productStock  = ArrayHelper::map($products, 'id', 'stock_qty');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['id' => 'invoice-form']); ?>

<div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'invoice_no')->textInput(['readonly' => true]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'customer_id')->dropDownList(
            ArrayHelper::map(Customer::find()->all(), 'id', 'name'),
            ['prompt' => 'Select Customer']
        ) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'invoice_date')->textInput(['type' => 'date']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'status')->dropDownList(['due' => 'Due', 'paid' => 'Paid', 'draft' => 'Draft']) ?>
    </div>
</div>

<h4 class="mt-3">Line Items</h4>
<table class="table table-bordered" id="items-table">
    <thead>
        <tr>
            <th style="width:40%">Product</th>
            <th style="width:15%">Qty</th>
            <th style="width:15%">Unit Price</th>
            <th style="width:20%">Line Total</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="items-body"></tbody>
</table>
<button type="button" class="btn btn-outline-primary btn-sm" id="add-item-btn">+ Add Item</button>

<div class="row mt-4">
    <div class="col-md-3 offset-md-6">
        <?= $form->field($model, 'discount')->textInput(['type' => 'number', 'step' => '0.01', 'id' => 'discount-input']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'tax')->textInput(['type' => 'number', 'step' => '0.01', 'id' => 'tax-input']) ?>
    </div>
</div>

<div class="text-end fs-5">
    Sub Total: <span id="sub-total-display">0.00</span> &nbsp;|&nbsp;
    <strong>Grand Total: <span id="grand-total-display">0.00</span></strong>
</div>

<div class="form-group mt-3">
    <?= Html::submitButton('Save Invoice', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$productMapJson = Json::htmlEncode($productMap);
$priceMapJson = Json::htmlEncode($productPrices);
$stockMapJson = Json::htmlEncode($productStock);

$js = <<<JS
const productMap = {$productMapJson};
const priceMap = {$priceMapJson};
const stockMap = {$stockMapJson};
let rowIndex = 0;

function buildOptions() {
    let opts = '<option value="">-- select product --</option>';
    for (const id in productMap) {
        opts += `<option value="\${id}">\${productMap[id]} (stock: \${stockMap[id]})</option>`;
    }
    return opts;
}

function addRow() {
    const tbody = document.getElementById('items-body');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select class="form-select product-select" name="items[\${rowIndex}][product_id]">\${buildOptions()}</select></td>
        <td><input type="number" min="1" class="form-control qty-input" name="items[\${rowIndex}][qty]" value="1"></td>
        <td><input type="text" class="form-control price-display" readonly value="0.00"></td>
        <td><input type="text" class="form-control line-total-display" readonly value="0.00"></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
    `;
    tbody.appendChild(tr);
    rowIndex++;
    bindRow(tr);
}

function bindRow(tr) {
    const select = tr.querySelector('.product-select');
    const qty = tr.querySelector('.qty-input');
    const priceDisp = tr.querySelector('.price-display');
    const lineDisp = tr.querySelector('.line-total-display');

    function recalcRow() {
        const price = parseFloat(priceMap[select.value] || 0);
        priceDisp.value = price.toFixed(2);
        const total = price * (parseFloat(qty.value) || 0);
        lineDisp.value = total.toFixed(2);
        recalcGrand();
    }

    select.addEventListener('change', recalcRow);
    qty.addEventListener('input', recalcRow);
    tr.querySelector('.remove-row').addEventListener('click', () => {
        tr.remove();
        recalcGrand();
    });
}

function recalcGrand() {
    let subTotal = 0;
    document.querySelectorAll('.line-total-display').forEach(el => {
        subTotal += parseFloat(el.value) || 0;
    });
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    const tax = parseFloat(document.getElementById('tax-input').value) || 0;
    document.getElementById('sub-total-display').innerText = subTotal.toFixed(2);
    document.getElementById('grand-total-display').innerText = (subTotal - discount + tax).toFixed(2);
}

document.getElementById('add-item-btn').addEventListener('click', addRow);
document.getElementById('discount-input').addEventListener('input', recalcGrand);
document.getElementById('tax-input').addEventListener('input', recalcGrand);

addRow(); // start with one row
JS;

$this->registerJs($js);
?>
