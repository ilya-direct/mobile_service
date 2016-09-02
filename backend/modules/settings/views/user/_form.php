<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var $this yii\web\View
 * @var $model \common\models\ar\User
 * @var $form yii\widgets\ActiveForm
 */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->widget(MaskedInput::className(),[
        'mask' => '+7 (999) 999 99 99',
    ]) ?>

    <?= $form->field($model, 'enabled')->checkbox(); ?>

    <?php if(!$model->isNewRecord): ?>
        <div class="form-group">
            <label>
                <?= Html::checkbox('recover-password', false, ['id' => 'user-recover-password']); ?>
                Восстановить пароль
            </label>
        </div>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
