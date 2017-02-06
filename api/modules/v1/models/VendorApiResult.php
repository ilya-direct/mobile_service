<?php

namespace api\modules\v1\models;

use api\components\app\ApiResultBase;
use common\models\ar\Vendor;

class VendorApiResult extends Vendor
{
    use ApiResultBase;
    
    
    protected function getResultAttributes()
    {
        return [
            'id',
            'name',
            'enabled',
            'alias',
        ];
    }
    
}
