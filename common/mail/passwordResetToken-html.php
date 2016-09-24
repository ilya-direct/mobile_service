<?php

use yii\helpers\Html;

/**
 * @var string $link ссылка перехода
 * @var yii\web\View $this
 * @var $user \common\models\ar\User
 */

$resetLink = Yii::$app->urlManagerBackend->createAbsoluteUrl([$link, 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Здравствуйте <?= Html::encode($user->first_name) ?>,</p>

    <p>Перейдите по следующей ссылке чтобы изменить пароль:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
