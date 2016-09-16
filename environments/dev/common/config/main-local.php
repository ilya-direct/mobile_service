<?php
return [
    'components' => [
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
        'urlManagerFrontend' => [
            'hostInfo' => 'http://host.name',
        ],
        'urlManagerBackend' => [
            'hostInfo' => 'http://host.name',
        ],
    ],
];
