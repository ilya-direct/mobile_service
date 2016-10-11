<?php
return [
    'login_only' => [
        'type' => 1,
    ],
    'operator' => [
        'type' => 1,
        'children' => [
            'login_only',
            'orderAccessOperator',
        ],
    ],
    'worker' => [
        'type' => 1,
        'children' => [
            'login_only',
            'orderAccessWorker',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'worker',
            'operator',
            'orderAccess',
        ],
    ],
    'orderAccess' => [
        'type' => 2,
        'description' => 'Проверка доступа к заказу для всех ролей',
        'ruleName' => 'OrderAdminRule',
    ],
    'orderAccessOperator' => [
        'type' => 2,
        'description' => 'Проверка доступа оператора к заказу',
        'ruleName' => 'OrderOperatorRule',
        'children' => [
            'orderAccess',
        ],
    ],
    'orderAccessWorker' => [
        'type' => 2,
        'description' => 'Проверка доступа мастера к заказу',
        'ruleName' => 'OrderWorkerRule',
        'children' => [
            'orderAccess',
        ],
    ],
];
