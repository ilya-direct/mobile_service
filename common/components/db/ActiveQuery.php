<?php

namespace common\components\db;

use Yii;

class ActiveQuery extends \yii\db\ActiveQuery
{

    /**
     * @var Имя таблицы у ActiveRecord
     */
    public $tableName;

    /**
     * Только неудалённые записи
     * @return $this
     */
    public function notDeleted()
    {
        return $this->andWhere([$this->tableName . '.' . '[[deleted]]'  => false]);
    }
}
