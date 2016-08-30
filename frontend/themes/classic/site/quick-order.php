<?php

/**
 * @var \yii\web\View $this
 * @var \common\models\ar\Order $order
 * @var \common\models\ar\OrderPerson $orderPerson
 */
$this->title = 'Оформление заявки';

?>

<div class="row">
    <div class="col-lg-4"></div>
    <div class="col-lg-4">
        <?= $this->render('//site/quick-order-form', [
            'order' => $order,
            'orderPerson' => $orderPerson,
            'full' => true,
        ]); ?>
    </div>
    <div class="col-lg-4"></div>
</div>
