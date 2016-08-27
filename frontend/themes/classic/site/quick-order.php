<?php

/**
 * @var \frontend\models\OrderModalForm $model
 * @var \yii\web\View $this
 */

$this->title = 'Оформление заявки';

?>

<div class="row">
    <div class="col-lg-4"></div>
    <div class="col-lg-4">
        <?= $this->render('//site/quick-order-form', ['model' => $model, 'id' => 'full']); ?>
    </div>
    <div class="col-lg-4"></div>
</div>
