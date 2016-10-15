<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \common\models\ar\Order $order
 * @var \yii\base\DynamicModel $deviceAssigns
 */
$this->title = 'Создание заказа';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'order' => $order,
        'deviceAssigns' => $deviceAssigns,
    ]); ?>

</div>
