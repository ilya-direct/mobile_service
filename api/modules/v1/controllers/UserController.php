<?php


namespace api\modules\v1\controllers;

use Yii;
use api\components\app\RestController;
use api\modules\v1\models\UserApiResult;

class UserController extends RestController
{
    public function actionView($id = null)
    {
        $id = $id ?: Yii::$app->user->id;
        $user = UserApiResult::findOneOrFail($id);
        
        return $user;
    }
}
