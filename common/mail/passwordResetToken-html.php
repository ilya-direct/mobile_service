<?php

use yii\helpers\Html;

/**
 * @var host string кастомный хост для url
 * @var yii\web\View $this
 * @var $user \common\models\ar\User
 */

if(!empty($host)){
    $oldHost=Yii::$app->urlManager->hostInfo;
    Yii::$app->urlManager->hostInfo=$host;
    $resetLink = Yii::$app->urlManager->createAbsoluteUrl([$link, 'token' => $user->password_reset_token]);
    Yii::$app->urlManager->hostInfo=$oldHost;
}else {
    $resetLink = Yii::$app->urlManager->createAbsoluteUrl([$link, 'token' => $user->password_reset_token]);
}
?>
<div class="password-reset">
    <p>Здравствуйте <?= Html::encode($user->first_name) ?>,</p>

    <p>Перейдите по следующей ссылке чтобы изменить пароль:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
