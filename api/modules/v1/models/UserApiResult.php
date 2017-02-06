<?php

namespace api\modules\v1\models;

use api\components\app\ApiResultBase;
use common\models\ar\User;

class UserApiResult extends User
{
    use ApiResultBase;
    
    protected function getResultAttributes()
    {
        return [
            'id',
            'email',
            'first_name',
            'last_name',
        ];
    }
}
