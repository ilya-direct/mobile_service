<?php
return [
    'login_only' => [
        'type' => 1,
    ],
    'operator' => [
        'type' => 1,
        'children' => [
            'login_only',
        ],
    ],
    'worker' => [
        'type' => 1,
        'children' => [
            'login_only',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'worker',
            'operator',
        ],
    ],
];
