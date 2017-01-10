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

        'session' => [
            'class' => yii\web\Session::className(),
            'timeout' => 5 * 365 * 24 * 60 * 60,
            'useCookies' => true,
            'name' => 'mobile-service',
        ],

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
                        'yii\swiftmailer\Mailer::sendMessage',
                        'yii\web\HttpException:403',
                    ]
                ],
            ],
        ],

        'authManager' => [
            'class' => common\components\app\PhpManager::className(),
            'itemFile' => '@common/rbac/items.php',
            'ruleFile' => '@common/rbac/rules.php',
        ],

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy H:i:s',
            'timeFormat' => 'H:i:s',
        ],

        'urlManagerFrontend' => require(dirname(dirname(__DIR__)) . '/frontend/config/url-manager.php'),
        'urlManagerBackend' => require(dirname(dirname(__DIR__)) . '/backend/config/url-manager.php'),
    
        'storage' => [
            'class' => common\components\app\StaticStorage::class,
            'nestingLevel' => 1, // Уровень вложенности(по умолчанию 1)
            'defaultFolder' => 'default', // Корневая поддирректория по умолчанию для сохранения
            'baseUrl' => 'http://static.mobile.dev', // Базовая ссылка к файлам
            'sections' => [ // Список всевозможных корневых поддирректорий
                'device-images',
            ],
            // Local filesystem
            'filesystem' => [
                'class' => creocoder\flysystem\LocalFilesystem::class,
                'path' => dirname(dirname(__DIR__)) . '/static', // Путь к корневой дирректории сохранения
            ],
        ],

    ],
];
