<?php

namespace api\modules\v1\models;

use api\components\app\ApiResultBase;
use common\models\ar\Device;

class DeviceApiResult extends Device
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
            'image_url',
            'deviceCategory' => [
                'id',
                'name',
            ],
            'vendor' => [
                'id',
                'name',
            ],
            'enabled',
        ];
    }
    
    
    public function afterFind()
    {
        $this->image_url = $this->getImageUrl();
    }
    
}
