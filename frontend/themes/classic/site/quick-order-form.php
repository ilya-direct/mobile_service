<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var \frontend\models\OrderModalForm $model
 * @var string $id
 */
?>

<?php $form = ActiveForm::begin(['action' => '/site/quick-order']); ?>
<?= $model->getFirstError('db'); ?>
<?= $form->field($model, 'name')->textInput()->label('Имя *'); ?>
<?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(),
    ['mask' => '+7 (999) 999-99-99', 'options' => [
        'id' => Html::getInputId($model, 'phone') . '-' . $id,
        'class' => 'form-control',
    ]])->label('Телефон *'); ?>
<?= $form->field($model, 'email')->textInput(); ?>
<?= $form->field($model, 'comment')->textarea(); ?>
<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>
<?php $form->end(); ?>

