<?php

namespace api\components\app;

use Yii;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
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
    
    public function actionOptions($actionId)
    {
        if (in_array($actionId, $this->actions()) || $this->hasMethod('action' . ucfirst($actionId))) {
            return;
        } else {
            throw new NotFoundHttpException('Action not found');
        }
    }
    
}
