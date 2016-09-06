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
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject([
            'class' => ActiveQuery::className(),
            'tableName' => static::tableName()
        ], [get_called_class()]);
    }

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

    /**
     * Найти или создать запись по условию, вернуть значение $attribute
     *
     * @param array|string $condition условие where()
     * @param string $attribute атрибут, который нужно вернуть (id по умолчанию)
     * @return string|int|boolean
     */
    public static function findOrCreateReturnScalar($condition, $attribute = 'id')
    {
        $value = static::find()
            ->select($attribute)
            ->where($condition)
            ->scalar();

        if ($value === false) {
            $model = new static($condition);
            $model->save(false);
            $value = $model->{$attribute};

        }

        return $value;
    }

    /**
     * Ассоциативный массив id => $attribute (по умолчанию name) с фильтром по условию $condition
     * @param array $condition
     * @param string $attribute
     * @return array
     */
    public static function getList(array $condition = [], $attribute = 'name')
    {
        $list =self::find()
            ->select($attribute)
            ->where($condition)
            ->indexBy('id')
            ->orderBy([$attribute => SORT_ASC])
            ->column();

        return $list;
    }
}
