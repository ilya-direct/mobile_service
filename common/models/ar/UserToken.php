<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property integer $id
 * @property string $value
 * @property integer $user_id
 * @property string $expire_date
 * @property string $created_at
 *
 * @property User $user
 */
class UserToken extends \yii\db\ActiveRecord
{
    const EXPIRE_TIME = 'P1D';
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
                'updatedAtAttribute' => false,
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'user_id' => 'User ID',
            'expires' => 'Expires',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @param $userId
     * @param null $value
     * @return UserToken
     */
    public static function create($userId, $value = null) {
        
        $dateTime = (new \DateTime())->add(new \DateInterval(self::EXPIRE_TIME));
        $userToken = new self([
            'user_id' => $userId,
            'value' => $value ?: Yii::$app->security->generateRandomString(),
            'expire_date' => $dateTime->format('Y-m-d H:i:s'),
        ]);
        
        $userToken->save(false);
        
        return $userToken;
    }
    
    public static function destroy($token)
    {
        $o = self::findOne(['value' => $token]);
        
        if ($o) {
            $o->delete();
        }
        
        return true;
    }
}
