<?php

namespace common\rbac\rules;

use yii\base\InvalidParamException;
use yii\rbac\Rule;
use common\models\ar\Order;
use common\models\ar\OrderStatus;
use common\models\ar\User;

class OrderAdminRule extends Rule
{
    public function execute($userId, $item, $params)
    {
        /** @var Order $order */
        $order = isset($params['order']) ? $params['order'] : null;
        $action = isset($params['action']) ? $params['action'] : null;

        if (!$order instanceof Order) {
            throw new InvalidParamException('Order param must be instance of Order');
        }
        if (!in_array($action, ['update', 'view'])) {
            throw new InvalidParamException('Action param must be update or view');
        }

        $access = true;
        if ($action == 'update') {
            $access = !empty(OrderStatus::availableStatuses($order->order_status_id, User::ROLE_ADMIN));
        }

        return $access;
    }
}
