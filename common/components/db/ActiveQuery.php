<?php

namespace common\components\db;

use Yii;

class ActiveQuery extends \yii\db\ActiveQuery
{

    /**
     * @var string Имя таблицы у ActiveRecord
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

    /**
     * Только активные записи
     * @return $this
     */
    public function enabled()
    {
        return $this->andWhere([$this->tableName . '.' . '[[enabled]]'  => true]);
    }
}
