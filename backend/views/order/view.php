<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\Order */

$this->title = $model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$services = $model->orderServices;

if ($model->address) {
    $this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
}
$this->registerJs(<<<JS
if (typeof  ymaps == 'object') {
    ymaps.ready(init);
    var myMap;
    function init() {
        myMap = new ymaps.Map("map", {
            center: [{$model->address_latitude}, {$model->address_longitude}],
            zoom: 11
        });
        var myGeocoder = ymaps.geocode('{$model->address}');
        myGeocoder.then(
            function (res) {
                //var coords = res.geoObjects.get(0).geometry.getCoordinates();
                myMap.geoObjects.add(res.geoObjects.get(0));
            }
        );
    }

    (function (d, w) {
        var s = d.createElement('div');
        s.id = 'map';
        s.style.width = '100%'
        s.style.minHeight =  '400px';
        s.style.marginBottom =  '10px';
        el = d.getElementsByClassName('col-md-6')[1];
        el.appendChild(s);
    })(document, window)
}
JS
);

?>
<div class="order-view">

    <h1 style="display: inline-block;"> Заказ  <?= Html::tag('strong', Html::encode($this->title)); ?></h1>

    <p style="display: inline-block;position:relative;bottom:8px;left:10px;">
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить заказ?',
                'method' => 'post',
            ],
        ]); ?>
        <?= Html::a('Создать новый заказ', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <?php
            $attributes = [];
            foreach ([
                'id',
                'uid',
                'orderStatus.name',
                'created_at',
                'orderProvider.name',
                'client_comment',
                'deviceProvider.name:ntext:Устройство в заказе',
                'preferable_date',
                'time_from',
                'time_to',
                'comment',
                'client_lead',
                'referer',
                'ip',
                'user_agent',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',
                     ] as $property) {
                if (mb_strpos($property, ':') !== false) {
                    $attributes[] = $property;
                } else {
                    $attributes[] = [
                        'attribute' => $property,
                        'visible' => !is_null(ArrayHelper::getValue($model, $property)),
                    ];
                }
            }
            ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $attributes,
            ]); ?>
            <h3>Контакты</h3>
            <?php
            $attributes = [];

            foreach (['first_name', 'last_name', 'middle_name', 'phone', 'email', 'address'] as $property) {
                $attributes[] = [
                    'attribute' => $property,
                    'visible' => isset($model->$property),
                ];
            }
            ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $attributes,
            ]); ?>
        </div>
        <div class="col-md-6">
            <!--Для Яндекс.Карт-->

        </div>
    </div>

    <?php if (!empty($services)) : ?>
        <p> Услуги</p>
        <table class="table">
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($services as $service): ?>
                <?php $price = $service->deviceAssign->revisionValue('price', $service->created_at); ?>
                <?php $total += $price; ?>
                <tr>
                    <td>
                        <?= $service->deviceAssign->device->revisionValue('name', $service->created_at); ?>
                    </td>
                    <td>
                        <?= $service->deviceAssign->service->revisionValue('name', $service->created_at); ?>
                    </td>
                    <td>
                        <?= $price; ?>
                    </td>
                </tr>
            <?php endForeach; ?>
            </tbody>
            <tr>
                <td colspan="3">
                    Итого: <?= $total; ?>
                </td>
            </tr>
        </table>
    <?php endif; ?>

</div>
