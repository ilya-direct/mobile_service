<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'mobile-api',
    'basePath' => dirname(__DIR__),
    'modules' => [
        'v1' => [
            'class' => api\modules\v1\V1::className(),
        ]
    ],
    'components' => [
        'user' => [
            'class' => api\components\app\User::className(),
            'identityClass' => common\models\ar\User::className(),
            'authTimeout' => 24 * 60 * 60,
        ],
        'urlManager' => require(__DIR__ . '/url-manager.php'),
        'request' => [
            'parsers' => [
                'application/json' => yii\web\JsonParser::class,
            ]
        ],
        'response' => [
            'class' => yii\web\Response::class,
            'format' => yii\web\Response::FORMAT_JSON,
        ],
    ],

    'params' => $params,
];
