<?php
use yii\helpers\ArrayHelper;

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'db' => ArrayHelper::merge(
            require __DIR__ . '/db/db.php',
            require __DIR__ . '/db/db-local.php'
        ),

    ],
];
