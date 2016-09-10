<?php

use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 */
$baseUrl = AppAsset::register($this)->baseUrl;

$this->title = 'Скидки';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="content_action">
    <div class = "row">
        <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <div class="bl_action">
                <h2>Скидка 5%</h2>
                <img src="<?= $baseUrl; ?>/images/action_img1.jpg" alt="" />
                <p>При заказе через сайт</p>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <div class="bl_action">
                <h2>Cashback 150 руб.</h2>
                <img src="<?= $baseUrl; ?>/images/action_img2.jpg" alt="" />
                <p>за отзыв</p>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="bl_action">
                <h2>СКИДКА 10%</h2>
                <img src="<?= $baseUrl; ?>/images/action_img3.jpg" alt="" />
                <p>10% на ремонт iphone <br /> ДО конца месяца</p>
            </div>
        </div>
    </div>
</div>
