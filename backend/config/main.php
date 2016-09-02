<?php

use common\models\ar\User;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'Mobile-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'settings' => [
            'class' => 'backend\modules\settings\Settings',
        ],
        'content' => [
            'class' => 'backend\modules\content\Content',
        ],

        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            'treeStructure' => [
                'depthAttribute' => 'depth',
            ],
            'dataStructure' => [
                'icon' => null,
                'icon_type' => 1,
            ]
            // other module settings, refer detailed documentation
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => User::className(),
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],

    'as beforeRequest' => [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'allow' => true,
                'controllers' => ['site'],
                'actions' => ['login', 'error', 'remember-password', 'reset-password'],
            ],
            [
                'allow' => false,
                'roles' => ['@'],
                'controllers' => ['site'],
                'actions' => ['reset-password'],
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
        'denyCallback' => function () {
            return Yii::$app->response->redirect(['site/login']);
        },
    ],

    'params' => $params,
];
