<?php

namespace common\models\ar;

use Yii;

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
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'created_at'], 'required'],
            [['description_short', 'description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['enabled'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description_short' => 'Description Short',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'enabled' => 'Enabled',
        ];
    }
}
