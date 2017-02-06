<?php

namespace api\modules\v1\models;

use api\components\app\ApiResultBase;
use common\models\ar\DeviceCategory;

class DeviceCategoryApiResult extends DeviceCategory
{
    use ApiResultBase;
    
    public $image_url;
    
    protected function getResultAttributes()
    {
        return [
            'id',
            'name',
            'alias',
            'description',
            'enabled',
        ];
    }
    
}
