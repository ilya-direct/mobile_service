<?php


namespace api\modules\v1\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use api\components\app\RestController;
use common\models\ar\Order;
use common\models\ar\UserToken;
use common\rbac\Permission;
use common\models\ar\User;

class UserController extends RestController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'roles' => ['?'],
            'actions'  => ['token'],
        ];
        
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'token' => ['post'],
                'detail' => ['get']
            ],
        ];
        
        return $behaviors;
    }
    
    public function actionToken()
    {
        $params = Yii::$app->request->bodyParams;
        $user = User::findByUsername($params['username']);
        
        if (!$user || !$user->validatePassword($params['password'])) {
            throw new BadRequestHttpException('Wrong email or password');
        }
        
        $token = UserToken::create($user->id);
        
        Yii::$app->user->login($user);
        
        return [
            'token' =>  $token->value,
            'expires' => $token->expire_date,
        ];
    }
    
    public function actionDetail()
    {
        $user = Yii::$app->user;
        
        return [
            'user-id' => $user->id,
            'is-guest' => $user->isGuest,
            'edit-order' => $user->can(Permission::ORDER_ACCESS, ['order' => Order::findOne(23), 'action' => 'view']),
        ];
    }
}
