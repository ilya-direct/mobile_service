<?php

namespace api\components\app;

use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\rest\OptionsAction;

class RestController extends Controller
{
    public function behaviors()
    {
        return [
            'cors' => [
                'class' => Cors::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['options'],
                    ],
                ],
            ],
        ];
    }
    
    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::className(),
            ],
        ];
    }
}
