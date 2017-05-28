<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

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
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    
        $jsonData = Json::encode([
            'token' => $this->value,
            'userId' => $this->user->id,
            'firstName' => $this->user->first_name,
            'lastName' => $this->user->last_name,
            'role' => $this->user->role,
            'expiresAt' => $this->expire_date,
        ]);
    
        $url = Yii::getAlias('@nodeApiUrl/token');
    
        $ch = curl_init();
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
//            'Host: node.mobile.local',
//            'Host: ' . parse_url($url)['host'],
//            'Accept: */*',
//            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
//            'Connection: keep-alive',
//            'Accept-Encoding: gzip, deflate, sdch',
//            'Upgrade-Insecure-Requests: 1',
//            'cache-control: no-cache',
        ];
    
        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
//            CURLOPT_REFERER => parse_url($url)['host'],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_ENCODING => '',
        ];
        $options[CURLOPT_CUSTOMREQUEST] = $insert ? 'POST' : 'PUT';
        $options[CURLOPT_POSTFIELDS] = $jsonData;
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);
    }
}
