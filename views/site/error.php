<?php
use yii\helpers\Html;
/** @var Throwable $exception */
$this->title = 'Error';
?>
<h1>An error occurred</h1>
<p><?= Html::encode($exception->getMessage()) ?></p>
