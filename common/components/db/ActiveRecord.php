<?php

namespace common\components\db;

use Yii;
use yii\db\Exception;

/**
 * Class ActiveRecord
 * @package common\components\db
 *
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @param $condition
     * @return null|static
     * @throws Exception
     */
    public static function findOneOrFail($condition)
    {
        $model = static::findOne($condition);

        if (!$model) {
            throw new Exception('Can\'t find by condition', $condition);
        }

        return $model;
    }

    /**
     * @param $condition
     * @return ActiveRecord
     */
    public static function findOrNew($condition)
    {
        $model = static::findOne($condition);

        if (!$model) {
            $model = new static($condition);
        }

        return $model;
    }
}
