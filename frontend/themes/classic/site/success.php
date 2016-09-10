<?php

use yii\helpers\Url;
use frontend\assets\AppAsset;

/**
 * @var yii\web\View $this
 * @var string $uid
 */
$baseUrl = AppAsset::register($this)->baseUrl;
$this->title = 'Заявка успешно оформлена';
?>
<div class="container">
    <div style="text-align: center; font-family: robotoregular;">
        <h1><span style="color: #00c962" class="glyphicon glyphicon-ok-circle"></span> Заявка успешно оформлена</h1>
        <p>Номер заявки:</p>
        <h2 style="margin-top: 0; font-weight: bold;text-transform: uppercase;"><?= $uid ?></h2>
        <a style="margin-top: 15px;background:#00c962" class="btn btn-success" href="<?= Url::home(); ?>">Перейти на главную</a>
    </div>
</div>
