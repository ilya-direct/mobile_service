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
$this->registerJs(<<<JS
 $(function() {
 $("a#delete-test").click(function(){
    event.preventDefault();
    var link = $(this);
     // Предотвращение double-click
    if (link.data('requestRunning')) {
        return false;
    }
    link.data('requestRunning', true);

    $.post(link.attr('href'), function(msg) {
        alert(msg.msg);
        link.data('requestRunning', false);
    });

    return false;
 });
});
JS
)
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p style="display: inline-block">
        <?= Html::a('Создать заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p style="display: inline-block">
        <?= Html::a('Удалить тестовые заказы', ['delete-test-orders'], ['id' => 'delete-test', 'class' => 'btn btn-default']) ?>
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
