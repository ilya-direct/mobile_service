<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\ar\User;
use common\rbac\Permission;
use common\rbac\rules\OrderOperatorRule;
use common\rbac\rules\OrderWorkerRule;
use common\rbac\rules\OrderAdminRule;

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

        // Order access
        $rule = new OrderAdminRule();
        $authManager->add($rule);
        $orderAccess = $authManager->createPermission(Permission::ORDER_ACCESS);
        $orderAccess->ruleName = $rule->name;
        $orderAccess->description = 'Проверка доступа к заказу для всех ролей';
        $authManager->add($orderAccess);

        $rule = new OrderOperatorRule();
        $authManager->add($rule);
        $orderAccessOperator = $authManager->createPermission(Permission::ORDER_ACCESS_OPERATOR);
        $orderAccessOperator->ruleName = $rule->name;
        $orderAccessOperator->description= 'Проверка доступа оператора к заказу';
        $authManager->add($orderAccessOperator);

        $rule = new OrderWorkerRule();
        $authManager->add($rule);
        $orderAccessWorker = $authManager->createPermission(Permission::ORDER_ACCESS_WORKER);
        $orderAccessWorker->ruleName = $rule->name;
        $orderAccessWorker->description= 'Проверка доступа мастера к заказу';
        $authManager->add($orderAccessWorker);


        $authManager->addChild($admin, $orderAccess);
        $authManager->addChild($operator, $orderAccessOperator);
        $authManager->addChild($worker, $orderAccessWorker);
        $authManager->addChild($orderAccessOperator, $orderAccess);
        $authManager->addChild($orderAccessWorker, $orderAccess);
    }
}
