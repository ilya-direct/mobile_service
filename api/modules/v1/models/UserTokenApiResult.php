<?php


namespace api\modules\v1\models;


use api\components\app\ApiResultBase;
use common\models\ar\UserToken;

class UserTokenApiResult extends UserToken
{
    use ApiResultBase;
    
    protected function getResultAttributes()
    {
        return [
            'value',
            'expire_date',
        ];
    }
}
