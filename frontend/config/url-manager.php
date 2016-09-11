<?php

return [
    'class' => yii\web\UrlManager::className(),
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'baseUrl' => '/',
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
    ]
];
