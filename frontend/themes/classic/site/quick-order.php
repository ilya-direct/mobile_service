<?php

/**
 * @var \yii\web\View $this
 * @var \common\models\ar\Order $order
 */
$this->title = 'Оформление заявки';

?>

<h2 style="margin-left:auto;margin-right:auto;width:300px;">Оформление заявки</h2>
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <?= $this->render('//site/quick-order-form', [
            'order' => $order,
            'full' => true,
        ]); ?>
    </div>
    <div class="col-sm-4"></div>
</div>
