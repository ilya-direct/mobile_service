<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use frontend\models\QuickOrderForm;

/**
 * @var QuickOrderForm $order
 * @var boolean $full Форма на отдельной странице
 */
$order = isset($order) ? $order : new QuickOrderForm();
?>

<?php $form = ActiveForm::begin(['action' => Url::to(['site/quick-order']), 'options' => ['id' => 'full-form']]); ?>
<?= $order->getFirstError('db'); ?>
<?= $form->field($order, 'first_name')->textInput()->label('Имя *'); ?>
<?= $form->field($order, 'phone')->widget(MaskedInput::className(),
    ['mask' => '+7 (999) 999-99-99'])->label('Телефон *'); ?>
<?= $form->field($order, 'email')->textInput(); ?>
<?= $form->field($order, 'client_comment')->textarea(['maxlength' => true])->label('Комментарий'); ?>
<?php if (!empty($full)): ?>
    <?= Html::hiddenInput('fullForm', true); ?>
<?php endif; ?>
<?= Html::submitButton('Отправить', ['class' => 'btn btn-success', 'style' => 'background:#00c962']); ?>
<?php $form->end(); ?>

