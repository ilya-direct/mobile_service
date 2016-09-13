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
    'homeUrl' => '/',
    'bootstrap' => ['log', 'deviceDetect'],
    'controllerNamespace' => 'frontend\controllers',
    'on beforeRequest' => function (yii\base\Event $event) {
        $request = Yii::$app->request;
        if (is_null(Yii::$app->session->get('referer'))) {
            $referer = $request->referrer ?: '';
            Yii::$app->session->set('referer', $referer);
            (new common\models\ar\FirstVisit([
                'session_id' => Yii::$app->session->id,
                'referer' => $request->referrer,
                'requested_url' => $request->url,
                'user_agent' => $request->userAgent,
                'time' => new \yii\db\Expression('NOW()'),
            ]))->save(false);
        }
        $pathInfo = $request->pathInfo;
        if (!empty($pathInfo) && mb_substr($pathInfo, -1) !== '/') {
            Yii::$app->response->redirect(array_merge([$pathInfo], $request->queryParams), 301);
            Yii::$app->end();
        }
    },
    'components' => [
        'deviceDetect' => [
            'class' => alexandernst\devicedetect\DeviceDetect::className(),
        ],
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
            'suffix' => '/',
            'rules' => [
                '/' => 'site/index',
                'quick-order' => 'site/quick-order', // верхняя форма заказа
                'contacts' => 'site/contacts',
                'about-us' => 'site/about-us',
                'footer-form' => 'site/footer-callback-form',
                'success' => 'site/success',
                'remont/<alias:[-\w]+>' => 'site/device',
                'category/<alias:[-\w]+>' => 'site/category',
                'category/<categoryAlias:[-\w]+>/brand/<vendorAlias:[-\w]+>' => 'site/vendor',
                'brand/<vendorAlias:[-\w]+>' => 'site/vendor',
                'discounts' => 'site/discounts',
            ],
        ],
    ],
    'params' => $params,
];
