<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var \frontend\models\OrderModalForm $model
 * @var boolean $full Форма на отдельной странице
 */
?>

<?php $form = ActiveForm::begin(['action' => '/site/quick-order', 'options' => ['id' => 'full-form']]); ?>
<?= $model->getFirstError('db'); ?>
<?= $form->field($model, 'name')->textInput()->label('Имя *'); ?>
<?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(),
    ['mask' => '+7 (999) 999-99-99'])->label('Телефон *'); ?>
<?= $form->field($model, 'email')->textInput(); ?>
<?= $form->field($model, 'comment')->textarea(); ?>
<?php if (!empty($full)): ?>
    <?= $form->field($model, 'fullForm')->hiddenInput(['value' => true])->label(false); ?>
<?php endif; ?>
<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>
<?php $form->end(); ?>

