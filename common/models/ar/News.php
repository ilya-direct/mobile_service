<?php

namespace common\models\ar;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\components\behaviors\RevisionBehavior;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property string $description_short
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enabled
 */
class News extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'enabled',
                    'description',
                    'description_short',
                    'title',
                ]
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['description_short', 'description'], 'string'],
            [['enabled'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['enabled'], 'filter', 'filter' => 'boolval'],
            [['description_short'], 'default']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'description_short' => 'Краткое описание',
            'description' => 'Описание',
            'enabled' => 'Показывать на сайте',
            'created_at' => 'Создана',
            'updated_at' => 'Последнее обновление',
        ];
    }
}
