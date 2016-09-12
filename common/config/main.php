<?php

use yii\helpers\ArrayHelper;

return [
    'name' => 'Mobile-service',
    'language' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['log'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'db' => ArrayHelper::merge(
            require __DIR__ . '/db/db.php',
            require __DIR__ . '/db/db-local.php'
        ),

        'log' => [
            'traceLevel' => YII_DEBUG ? 0 : 0,
            'targets' => [
                [
                    'class' => yii\log\DbTarget::className(),
                    'levels' => ['error', 'warning', 'info'],
                    'except' => [
                        'yii\web\Session::open',
                        'yii\db\Connection::open',
                        'yii\db\Command::execute',
                        'yii\db\Command::query',
                    ]
                ],
            ],
        ],

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy H:i:s',
            'timeFormat' => 'H:i:s',
        ],

        'urlManagerFrontend' => require(dirname(dirname(__DIR__)) . '/frontend/config/url-manager.php'),
        'urlManagerBackend' => require(dirname(dirname(__DIR__)) . '/backend/config/url-manager.php'),

    ],
];
