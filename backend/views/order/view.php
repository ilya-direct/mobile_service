<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\Order */

$this->title = $model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$services = $model->orderServices;
?>
<div class="order-view">

    <h1 style="display: inline-block;"> Заказ  <?= Html::tag('strong', Html::encode($this->title)); ?></h1>

    <p style="display: inline-block;position:relative;bottom:8px;left:10px;">
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить заказ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uid',
            'orderStatus.name',
            'created_at',
            'order_person_id',
            'orderProvider.name',
            'preferable_date',
            'time_from',
            'time_to',
            'comment',
            'referer',
            'ip',
        ],
    ]); ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'orderPerson.first_name',
            'orderPerson.last_name',
            'orderPerson.middle_name',
            'orderPerson.phone',
            'orderPerson.email',
            'orderPerson.address',
        ],
    ]); ?>

    <?php if (!empty($services)) : ?>
        <p> Услуги</p>
        <table class="table">
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($services as $service): ?>
                <?php $total += $service->deviceAssign->price; ?>
                <tr>
                    <td>
                        <?= $service->deviceAssign->device->name; ?>
                    </td>
                    <td>
                        <?= $service->deviceAssign->service->name; ?>
                    </td>
                    <td>
                        <?= $service->deviceAssign->price; ?>
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
