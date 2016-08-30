<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use common\models\ar\OrderStatus;
use dosamigos\datepicker\DatePicker;

/**
 * @var yii\web\View $this
 * @var \common\models\ar\Order $order
 * @var \common\models\ar\OrderPerson $orderPerson
 * @var \yii\base\DynamicModel $deviceAssigns
 */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-5 well" style="margin-left: 10px;margin-right: 10px;">
            <h4>Данные клиента</h4>
            <?= $form->field($orderPerson, 'first_name')->textInput(['maxlength' => true]); ?>
            <?= $form->field($orderPerson, 'last_name')->textInput(['maxlength' => true]); ?>
            <?= $form->field($orderPerson, 'middle_name')->textInput(['maxlength' => true]); ?>
            <?= $form->field($orderPerson, 'phone')->widget(MaskedInput::className(),
                ['mask' => '+7 (999) 999-99-99']); ?>
            <?= $form->field($orderPerson, 'email')->textInput(); ?>
            <?= $form->field($orderPerson, 'address')->textInput(['maxlength' => true]); ?>
            <a href="https://yandex.ru/maps/" target="_blank">Найти адрес на карте</a>
        </div>
        <div class="col-lg-6 well" style="margin-left: 10px;margin-right: 10px;">
            <h4>Данные заказа</h4>
            <?= $form->field($order, 'order_status_id')->dropDownList(OrderStatus::getList(), ['prompt' => '...']); ?>
            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($order, 'preferable_date')->widget(DatePicker::className(), [
                            'language' => 'ru',
                            'clientOptions' => [
                                'minView' => 0,
                                'maxView' => 1,
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                                'todayHighlight' => true,
                                'todayBtn' => 'linked',
                                'startDate' => 'new Date(' . time() * 1000 . ')',
                            ]]); ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($order, 'time_from')->textInput(); ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($order, 'time_to')->textInput(); ?>
                </div>
            </div>
            <?= $form->field($order, 'client_lead')->textInput(); ?>
            <?= $form->field($deviceAssigns, 'deviceAssignIds')->textInput()->label('ID услуг по устройствам через запятую'); ?>
            <?= $form->field($order, 'comment')->textarea(); ?>

        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($order->isNewRecord ? 'Создать заказ' : 'Сохранить', ['class' => $order->isNewRecord ? 'btn btn-success' : 'btn btn-primary']); ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
