<?php

use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 */
$baseUrl = AppAsset::register($this)->baseUrl;
?>
<div class="delivery">
    <h2>Курьерская доставка</h2>
    <h3>Вам необязательно выезжать к нам в сервис, чтобы отремонтировать...</h3>
    <img src="<?= $baseUrl; ?>/images/delivery_img.jpg" alt="" />
    <a href="#">подробнее</a>
</div>
