<?php

namespace common\models\ar;

use Yii;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "revision_table".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Revision[] $revisions
 */
class RevisionTable extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'revision_table';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название таблицы по ревизии',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisions()
    {
        return $this->hasMany(Revision::className(), ['revision_table_id' => 'id']);
    }
}
