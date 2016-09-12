<?php

use yii\helpers\Html;

/**
 * @var string $link ссылка на заказ
 * @var string $uid номер заказа
 * @var yii\web\View $this
 */
?>
<div class="password-reset">
    <p>Поступил новый заказ номер <strong><?= Html::encode($uid); ?></strong> </p>
    <p>Ссылка на заказ</p>
    <p><?= Html::a(Html::encode($link), $link) ?></p>
</div>
