<?php

namespace api\components\app;

use Yii;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

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
    
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }
    
    public function actionOptions($id = 0)
    {
        $method = Yii::$app->request->method;
    
//        in_array($method, $this->actions()) || $this->hasMethod('action' . ucfirst($method))
        
        return;
    }
    
}
