<?php
use yii\helpers\ArrayHelper;

return [
    'name' => 'Mobile-service',
    'language' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'db' => ArrayHelper::merge(
            require __DIR__ . '/db/db.php',
            require __DIR__ . '/db/db-local.php'
        ),

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy H:i:s',
            'timeFormat' => 'H:i:s',
        ]

    ],
];
