<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\ar\User;

/**
 * Инициализация ролей, разрешений, правил
 *
 * @package console\controllers
 */
class RbacController extends Controller
{
    public function actionInit()
    {
        $authManager = Yii::$app->authManager;
        $authManager->removeAll();

        // Create roles
        $loginOnly  = $authManager->createRole(User::ROLE_LOGIN_ONLY);
        $worker = $authManager->createRole(User::ROLE_WORKER);
        $operator  = $authManager->createRole(User::ROLE_OPERATOR);
        $admin  = $authManager->createRole(User::ROLE_ADMIN);

        // Add roles in Yii::$app->authManager
        $authManager->add($loginOnly);
        $authManager->add($operator);
        $authManager->add($worker);
        $authManager->add($admin);

        // Operator
        $authManager->addChild($operator, $loginOnly);

        // Worker
        $authManager->addChild($worker, $loginOnly);

        // Admin
        $authManager->addChild($admin, $worker);
        $authManager->addChild($admin, $operator);

    }
}
