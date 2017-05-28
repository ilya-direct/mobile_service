<?php


namespace api\modules\v1\models;


use api\components\app\ApiResult;

class OrderApiResult extends ApiResult
{
    protected static function getResultAttributes()
    {
        return [
            'id',
            'uid',
            'created_at',
            'created_by',
            'orderStatus' =>[
                'id',
                'name'
            ],
            'preferable_date',
            'comment',
            'client_lead',
        ];
    }
}