<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%first_visit}}".
 *
 * @property integer $id
 * @property string $session_id
 * @property string $requested_url
 * @property string $referer
 * @property string $user_agent
 * @property string $time
 */
class FirstVisit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%first_visit}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'ID сессии',
            'requested_url' => 'Первая страница, на которую перешёл клиент',
            'referer' => 'Откуда был заход',
            'user_agent' => 'User Agent',
            'time' => 'Time',
        ];
    }
}
