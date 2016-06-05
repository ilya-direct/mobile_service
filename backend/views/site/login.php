<?php

/**
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \common\models\LoginForm
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Заполните поля, чтобы войти</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php \yii\bootstrap\Modal::begin([
                'header' => Html::tag('h2', 'Восстановление пароля'),
                'toggleButton' => [
                    'tag' => 'a',
                    'label' => 'Забыли пароль?',
                ],
            ]) ?>
            <?= Html::beginForm(['/site/remember-password'], 'post') ?>
            <?= Html::input('text', 'email', null, ['class' => 'form-control']) ?>
            <?= Html::submitButton('Восстановить пароль', ['class' => 'btn bln-primary']) ?>
            <?php Html::endForm() ?>
            <?php \yii\bootstrap\Modal::end(); ?>

        </div>
    </div>
</div>
