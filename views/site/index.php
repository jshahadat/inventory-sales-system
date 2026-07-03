<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var app\models\Product[] $lowStock */
/** @var app\models\SalesInvoice[] $recentInvoices */
/** @var float $todaySalesTotal */

$this->title = 'Dashboard';
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Today's Sales</h5>
                <p class="card-text fs-3"><?= number_format($todaySalesTotal, 2) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Low Stock Products</h5>
                <p class="card-text fs-3"><?= count($lowStock) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h4>Low Stock Alert</h4>
        <table class="table table-sm table-striped">
            <thead><tr><th>SKU</th><th>Product</th><th>Stock</th><th>Reorder Level</th></tr></thead>
            <tbody>
            <?php foreach ($lowStock as $p): ?>
                <tr class="table-danger">
                    <td><?= Html::encode($p->sku) ?></td>
                    <td><?= Html::a(Html::encode($p->name), ['/product/view', 'id' => $p->id]) ?></td>
                    <td><?= $p->stock_qty ?></td>
                    <td><?= $p->reorder_level ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($lowStock)): ?>
                <tr><td colspan="4" class="text-muted">No low stock items. Good job!</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        <h4>Recent Invoices</h4>
        <table class="table table-sm table-striped">
            <thead><tr><th>Invoice No</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($recentInvoices as $inv): ?>
                <tr>
                    <td><?= Html::a(Html::encode($inv->invoice_no), ['/sales-invoice/view', 'id' => $inv->id]) ?></td>
                    <td><?= $inv->invoice_date ?></td>
                    <td><?= number_format($inv->grand_total, 2) ?></td>
                    <td><span class="badge bg-secondary"><?= $inv->status ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
