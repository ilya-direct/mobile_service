<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'on beforeRequest' => function (yii\base\Event $event) {
        if (is_null(Yii::$app->session->get('referer'))) {
            $referer = Yii::$app->request->referrer ?: '';
            Yii::$app->session->set('referer', $referer);
        }
    },
    'components' => [
        'user' => [
            'identityClass' => common\models\ar\Customer::className(),
            'enableAutoLogin' => true,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'view' => [
            'theme' => [
                'pathMap' => ['@app/views' => '@app/themes/classic'],
                'baseUrl' => '@web/themes/classic',
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
