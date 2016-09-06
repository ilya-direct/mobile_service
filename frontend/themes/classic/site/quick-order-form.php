<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/**
 * @var \common\models\ar\Order $order
 * @var \common\models\ar\OrderPerson $orderPerson
 * @var boolean $full Форма на отдельной странице
 */
?>

<?php $form = ActiveForm::begin(['action' => Url::to(['site/quick-order']), 'options' => ['id' => 'full-form']]); ?>
<?= $order->getFirstError('db'); ?>
<?= $form->field($orderPerson, 'first_name')->textInput()->label('Имя *'); ?>
<?= $form->field($orderPerson, 'phone')->widget(MaskedInput::className(),
    ['mask' => '+7 (999) 999-99-99'])->label('Телефон *'); ?>
<?= $form->field($orderPerson, 'email')->textInput(); ?>
<?= $form->field($order, 'comment')->textarea(); ?>
<?php if (!empty($full)): ?>
    <?= Html::hiddenInput('fullForm', true); ?>
<?php endif; ?>
<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>
<?php $form->end(); ?>

