<?php

namespace api\modules\v1\models;

use api\components\app\ApiResultBase;
use common\models\ar\User;
use common\models\ar\UserToken;

class UserTokenApiResult extends UserToken
{
    use ApiResultBase;
    
    public $user;
    
    protected function getResultAttributes()
    {
        return [
            'value',
            'expire_date',
            'user' => [
                'id',
                'email',
                'first_name',
            ],
        ];
    }
    
    public function init()
    {
        parent::init();
        $this->user = User::findOne($this->user_id);
    }
}
