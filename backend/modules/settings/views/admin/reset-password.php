<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var $model \common\models\ResetPasswordForm
 */

$this->title = 'Создание нового пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Пожалуйста, введите свой новый пароль:</p>

    <div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'reset-password']); ?>
        <?= $form->field($model, 'password')->passwordInput(); ?>
        <?= $form->field($model, 'passwordRepeat')->passwordInput(); ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>