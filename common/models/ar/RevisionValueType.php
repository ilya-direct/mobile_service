<?php

namespace common\models\ar;

use Yii;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "revision_value_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Revision[] $revisions
 */
class RevisionValueType extends ActiveRecord
{
    /**
     * @var array список функции преведения типов
     */
    public static $castFunctions= [
        'integer' => 'intval',
        'string' => 'strval',
        'boolean' => 'boolval',
        'double' => 'doubleval',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%revision_value_type}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название типа данных',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisions()
    {
        return $this->hasMany(Revision::className(), ['revision_value_type_id' => 'id']);
    }
}
