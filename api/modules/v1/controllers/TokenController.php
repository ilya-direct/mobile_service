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
            throw new BadRequestHttpException('Email not found');
        }
        
        if (!$user->validatePassword($params['password'])) {
            throw new BadRequestHttpException('Wrong email or password');
        }
        
        $token = new UserTokenApiResult(UserToken::create($user->id));
        
        return $token;
    }
}
