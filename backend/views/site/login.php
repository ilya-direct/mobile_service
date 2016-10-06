<?php

/**
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \backend\models\LoginForm
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;

$onHidden = <<<JS
function () {
    $('#modal-msg').text('');
    $('#forgot-password').show();
}
JS;


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
                'header' => Html::tag('h4', 'Восстановление пароля', ['class' => 'modal-title']),
                'toggleButton' => [
                    'tag' => 'a',
                    'label' => 'Забыли пароль?',
                ],
                'clientEvents' => [
                    'hidden.bs.modal' => $onHidden,
                ]
            ]) ?>

            <div id="modal-msg"></div>

            <?= Html::beginForm(['/site/remember-password'], 'post', ['id' => 'forgot-password']) ?>
            <div class="form-group">
                <label for="recipient-name" class="control-label">Введите свой Email:</label>
                <?= Html::input('text', 'email', null, ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Восстановить пароль', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php Html::endForm() ?>
            <?php \yii\bootstrap\Modal::end(); ?>

        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS
$('#forgot-password').submit(function () {
    var formData = {'email': $(this).find('input[name=email]')[0].value};
    $.ajax({
        url :'/site/remember-password',
        method : 'post',
        data: formData,
        dataType : 'json',
        success : function(data){
            $('#forgot-password').hide();
            $('#modal-msg').text(data.msg);
        }
    });
    return false;
});
JS
);