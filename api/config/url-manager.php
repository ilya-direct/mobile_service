<?php

return [
    'class' => \yii\web\UrlManager::className(),
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'baseUrl' => '/',
    'rules' => [
//        'OPTIONS v1/<controller:\w+>/<actionId:\w+>' => 'v1/<controller>/options',
        [
            'class' => yii\rest\UrlRule::className(),
            'controller' => 'v1/device',
            'extraPatterns' => [
                'POST image-upload' => 'image-upload',
                'OPTIONS image-upload' => 'options',
            ]
        ],
        [
            'class' => yii\rest\UrlRule::className(),
            'controller' => 'v1/device-category',
        ],
        [
            'class' => yii\rest\UrlRule::className(),
            'controller' => 'v1/vendor',
        ],
        [
            'class' => yii\rest\UrlRule::className(),
            'controller' => 'v1/token',
        ],
        [
            'class' => yii\rest\UrlRule::className(),
            'controller' => 'v1/user',
            'extraPatterns' => [
                'GET,HEAD current' => 'view',
            ],
        ],
    ],
];
