<?php

return [
    'class' => \yii\web\UrlManager::className(),
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'baseUrl' => '/',
    'rules' => [
        'OPTIONS v1/<controller:\w+>/<actionId:\w+>' => 'v1/<controller>/options',
    ],
];
