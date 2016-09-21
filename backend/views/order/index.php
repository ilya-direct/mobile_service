<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ar\OrderStatus;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \backend\models\OrderSearchForm $searchModel
 */
$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'uid',
            [
                'attribute' => 'order_status_id',
                'value' => 'orderStatus.name',
                'filter' => OrderStatus::getList(),
            ],
            [
                'label' => 'Имя',
                'attribute' => 'name',
                'value' => 'orderPerson.first_name',
            ],
            [
                'label' => 'Телефон',
                'attribute' => 'phone',
                'value' => 'orderPerson.phone',
            ],
            'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
