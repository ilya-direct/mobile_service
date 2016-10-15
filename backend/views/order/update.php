<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \common\models\ar\Order $order
 * @var \yii\base\DynamicModel $deviceAssigns
 */
$this->title = 'Заказ ' . $order->uid;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $order->uid, 'url' => ['view', 'id' => $order->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="order-update">

    <h1><?= 'Редактирование заказа ' . Html::tag('strong', $order->uid) ?></h1>

    <?= $this->render('_form', [
        'order' => $order,
        'deviceAssigns' => $deviceAssigns,
    ]); ?>

</div>
