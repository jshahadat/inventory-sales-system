<?php

/** @var app\models\SalesInvoice $model */
$company = Yii::$app->params['companyName'] ?? 'Your Company';
$address = Yii::$app->params['companyAddress'] ?? '';
?>
<html>
<head>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #222; }
    h1 { font-size: 20px; margin-bottom: 0; }
    .muted { color: #777; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
    th { background: #f2f2f2; }
    .text-end { text-align: right; }
    .totals td { border: none; padding: 3px 8px; }
    .header-row { width: 100%; }
</style>
</head>
<body>

<table class="header-row" style="border:none;">
<tr style="border:none;">
    <td style="border:none; width:60%;">
        <h1><?= htmlspecialchars($company) ?></h1>
        <div class="muted"><?= htmlspecialchars($address) ?></div>
    </td>
    <td style="border:none; text-align:right;">
        <h2>INVOICE</h2>
        <div><strong>No:</strong> <?= htmlspecialchars($model->invoice_no) ?></div>
        <div><strong>Date:</strong> <?= htmlspecialchars($model->invoice_date) ?></div>
        <div><strong>Status:</strong> <?= htmlspecialchars(ucfirst($model->status)) ?></div>
    </td>
</tr>
</table>

<p>
    <strong>Bill To:</strong><br>
    <?= htmlspecialchars($model->customer->name) ?><br>
    <?= htmlspecialchars($model->customer->address) ?><br>
    <?= htmlspecialchars($model->customer->phone) ?>
</p>

<table>
    <thead>
        <tr>
            <th>#</th><th>Product</th><th>Qty</th><th>Unit Price</th><th>Line Total</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($model->items as $i => $item): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($item->product->name) ?></td>
            <td><?= $item->qty ?></td>
            <td><?= number_format($item->unit_price, 2) ?></td>
            <td><?= number_format($item->line_total, 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table style="width:40%; margin-left:60%;" class="totals">
    <tr><td>Sub Total</td><td class="text-end"><?= number_format($model->sub_total, 2) ?></td></tr>
    <tr><td>Discount</td><td class="text-end">- <?= number_format($model->discount, 2) ?></td></tr>
    <tr><td>Tax</td><td class="text-end">+ <?= number_format($model->tax, 2) ?></td></tr>
    <tr><td><strong>Grand Total</strong></td><td class="text-end"><strong><?= number_format($model->grand_total, 2) ?></strong></td></tr>
</table>

<p class="muted" style="margin-top:40px;">Thank you for your business.</p>

</body>
</html>
