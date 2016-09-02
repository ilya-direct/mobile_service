<?php

/**
 * @var $host string кастомный хост для url
 * @var $this  yii\web\View
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
Здравствуйте <?= $user->first_name; ?>,

Используйте ссылку ниже чтобы изменить пароль:

<?= $resetLink ?>
