<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\UserTokenApiResult;
use common\models\ar\User;
use common\models\ar\UserToken;
use Yii;
use api\components\app\RestController;
use yii\web\BadRequestHttpException;

class TokenController extends RestController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
    
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'roles' => ['?'],
            'actions'  => ['create'],
        ];
        
        return $behaviors;
    }
    
    public function actionCreate()
    {
        $params = Yii::$app->request->bodyParams;
        $user = User::findByUsername($params['username']);
        
        if (!$user) {
            Yii::$app->response->setStatusCode(400, 'Wrong Validation');
    
            return ['username' => 'Не найдено'];
        }
        
        if (!$user->validatePassword($params['password'])) {
            Yii::$app->response->setStatusCode(400, 'Wrong Validation');
    
            return ['password' => 'Неверный пароль'];
        }
        
        $token = new UserTokenApiResult(UserToken::create($user->id));
        
        return $token;
    }
}
