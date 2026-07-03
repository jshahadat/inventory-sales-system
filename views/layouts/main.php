<?php

/** @var \yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?> | Inventory &amp; Sales System</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php if (!Yii::$app->user->isGuest): ?>
<?php
NavBar::begin([
    'brandLabel' => 'Inventory &amp; Sales System',
    'brandUrl' => Yii::$app->homeUrl,
    'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top'],
]);

$items = [
    ['label' => 'Dashboard', 'url' => ['/site/index']],
];

if (Yii::$app->user->can('viewProduct')) {
    $items[] = ['label' => 'Products', 'url' => ['/product/index']];
}
if (Yii::$app->user->can('manageProduct')) {
    $items[] = ['label' => 'Categories', 'url' => ['/category/index']];
    $items[] = ['label' => 'Suppliers', 'url' => ['/supplier/index']];
}
if (Yii::$app->user->can('viewInvoice')) {
    $items[] = ['label' => 'Sales Invoices', 'url' => ['/sales-invoice/index']];
    $items[] = ['label' => 'Customers', 'url' => ['/customer/index']];
}

$items[] = '<li class="nav-item"><span class="nav-link text-white-50">'
    . Html::encode(Yii::$app->user->identity->username)
    . ' (' . Html::encode(Yii::$app->user->identity->getRoleName() ?? '-') . ')</span></li>';

$items[] = '<li>' . Html::beginForm(['/site/logout'], 'post')
    . Html::submitButton('Logout', ['class' => 'btn btn-link nav-link'])
    . Html::endForm() . '</li>';

echo Nav::widget([
    'options' => ['class' => 'navbar-nav ms-auto'],
    'items' => $items,
]);
NavBar::end();
?>
<?php endif; ?>

<div class="container" style="margin-top: <?= Yii::$app->user->isGuest ? '20px' : '70px' ?>;">
    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]) ?>

    <?php foreach (['success', 'error', 'warning', 'info'] as $type): ?>
        <?php if (Yii::$app->session->hasFlash($type)): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?>">
                <?= Yii::$app->session->getFlash($type) ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?= $content ?>
</div>

<footer class="footer text-center text-muted py-4 mt-5 border-top">
    &copy; <?= date('Y') ?> <?= Yii::$app->params['companyName'] ?? 'Company' ?>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
