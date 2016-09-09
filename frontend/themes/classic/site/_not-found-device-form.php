<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use frontend\models\NotFoundDeviceForm;

/**
 * @var \yii\web\View $this
 * @var NotFoundDeviceForm|null $model
 */
$model = isset($model) ? $model : new NotFoundDeviceForm;
?>
<div class="device-not-found-container">
    <div class="device-not-found-form">
        <h2>Не нашли нужную модель?</h2>
        <h3>Позвоните по тел. <span>+7 (963) 656 83 77</span> или <span>заполните форму</span> и мы свяжемся с вами</h3>
        <?php $form = ActiveForm::begin([
            'id' => 'not-found-device-form',
            'action' => ['site/not-found-device'],
            'options' => [
                'class' => '',
            ],
            'fieldConfig' => [
                'options' => ['class' => 'input'],
            ],]);
        ?>
        <div class="row">
            <?= $form->field($model, 'db', ['options' => ['class' => '', 'style' => 'text-align: center;']])
                ->hiddenInput()
                ->label(false); ?>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
            </div>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
                    'mask' => '+7 (999) 999-99-99',
                ]); ?>
            </div>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'device')->textInput(['maxlength' => true]); ?>
            </div>
            <div class = "col-xs-12">
                <?= Html::submitButton('Перезвонить'); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJs(<<<JS
// Отправка формы "Не нашли нужную модель" аяксом
$('#not-found-device-form').on('beforeSubmit', activeFormAjax);
JS
);
$this->registerCss(<<<CSS

.device-not-found-container {
    background-color: #F8F8F8;
    border-top: 1px solid #E4E4E4;
    border-bottom: 1px solid #E4E4E4;
    margin-top: 20px;
    padding: 30px 0 15px 0;
}
.device-not-found-form {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 10px;
}

.device-not-found-form h2 {
    color: #393737;
    font: 25px/1.1 robotoregular;
    text-align: center;
}

.device-not-found-form h3 {
    color: #393737;
    font: 15px/1.1 robotolight;
    text-align: center;
    margin-top: 18px;
}

.device-not-found-form form {
    margin-top: 10px;
}

.device-not-found-form .input {
    margin-top: 30px;
}
.device-not-found-form .input label {
    display: block;
    color: #5D5D5D;
    font: 14px/1.1 robotolight;
    text-transform: uppercase;
    margin-left: 2px;
}

.device-not-found-form .input input {
    width: 99%;
    height: 50px;
    display: block;
    color: #5D5D5D;
    font: 14px/1.1 robotolight;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    background-color: #fff;
    -webkit-box-shadow: inset 0 0 5px rgba(172, 172, 172, .75);
    -moz-box-shadow: inset 0 0 5px rgba(172, 172, 172, .75);
    box-shadow: inset 0 0 5px rgba(172, 172, 172, .75);
    border: solid 1px #e5e5e5;
    outline: none;
    padding-left: 10px;
    margin-top: 8px;
}

.device-not-found-form button {
    display: block;
    background-color: #00C962;
    width: 180px;
    height: 33px;
    margin: 0 auto;
    color: #fff;
    font: 13px/33px robotoregular;
    text-align: center;
    border: none;
    outline: none;
    border-radius: 5px;
    margin-top: 25px;
    text-transform: uppercase;
}
CSS
);