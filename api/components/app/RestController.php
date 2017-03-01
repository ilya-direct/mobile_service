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
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],
                ],
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
