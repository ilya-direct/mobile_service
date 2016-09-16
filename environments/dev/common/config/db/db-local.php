<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=dbname',
    'username' => 'username',
    'password' => 'password',
    'tablePrefix' => 'prefix',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql'=> [
            'class'=>\yii\db\pgsql\Schema::class,
            'defaultSchema' => 'public' //specify your schema here
        ]
    ],
];
