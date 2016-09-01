<?php

namespace common\models\ar;

use Yii;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "revision_field".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Revision[] $revisions
 */
class RevisionField extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%revision_field}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название поля таблицы по ревизии',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisions()
    {
        return $this->hasMany(Revision::className(), ['revision_field_id' => 'id']);
    }
}
