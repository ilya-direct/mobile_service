<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\IdentityInterface;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $address
 * @property double $address_latitude
 * @property double $address_longitude
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $phone
 * @property boolean $enabled
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $deleted
 * @property string $role роль пользователя
 *
 * @property News[] $newsCreator
 * @property News[] $newsUpdater
 * @property Order[] $ordersCreator
 * @property Order[] $ordersUpdater
 * @property Revision[] $revisions
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_LOGIN_ONLY = 'login_only';
    const ROLE_WORKER = 'worker';
    const ROLE_OPERATOR = 'operator';
    const ROLE_ADMIN = 'admin';

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['deleted'],
                ],
                'value' => false,
            ],
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'email',
                    'first_name',
                    'last_name',
                    'middle_name',
                    'address',
                    'address_latitude',
                    'address_longitude',
                    'phone',
                    'enabled',
                    'deleted',
                    'role',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'first_name','phone', 'enabled', 'role'], 'required'],
            [['address_latitude', 'address_longitude'], 'number'],
            [['enabled'], 'boolean'],
            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 50],
            ['address', 'string', 'max' => 255],
            [['email'], 'unique'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3} \d{2} \d{2}$/',
                'message' => 'Формат: +7 (ХХХ) ХХХ ХХ ХХ',
            ],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['enabled', 'filter', 'filter' => 'boolval'],
            ['role', 'in', 'range' => self::getRoles()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'address' => 'Адрес',
            'address_latitude' => 'Address Latitude',
            'address_longitude' => 'Address Longitude',
            'password_reset_token' => 'Password Reset Token',
            'phone' => 'Телефон',
            'enabled' => 'Активен',
            'created_at' => 'Время создания',
            'updated_at' => 'Время последнего обновления',
            'role' => 'Роль',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewsCreator()
    {
        return $this->hasMany(News::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewsUpdater()
    {
        return $this->hasMany(News::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderCreator()
    {
        return $this->hasMany(Order::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersUpdater()
    {
        return $this->hasMany(Order::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisions()
    {
        return $this->hasMany(Revision::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'enabled' => true, 'deleted' => false]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return false;
    }

    /**
     * Finds user by username  (email)
     *
     * @param string $email
     * @return static|null
     */
    public static function findByUsername($email)
    {
        return static::findOne(['email' => $email, 'enabled' => true, 'deleted' => false]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'enabled' => 1,
            'deleted' => false,
        ]);
    }
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function delete($soft = true)
    {
        if ($soft) {
            $this->deleted = true;
            return $this->update(false, ['deleted']);
        }

        return parent::delete();
    }

    /*
     * Sends reset password message
     * @param User $user
     * @return boolean
     */
    public function sendResetPasswordMail()
    {
        $this->generatePasswordResetToken();
        $this->update(false, ['password_reset_token']);
        return Yii::$app->mailer->compose(['html' => 'passwordResetToken-html'], [
            'user' => $this,
            'link' => 'site/reset-password',
        ])
            ->setFrom([Yii::$app->params['appEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject('Восстановление пароля для ' . Yii::$app->name)
            ->send();
    }
    
    /**
     * Роль -> название роли
     * @return array
     */
    public static function getRolesList()
    {
        return [
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_WORKER => 'Мастер',
            self::ROLE_OPERATOR => 'Оператор',
            self::ROLE_LOGIN_ONLY => 'Базовый пользователь',
        ];
    }
    
    
    /**
     * Список ролей
     * @return array
     */
    public static function getRoles()
    {
        return array_keys(self::getRolesList());
    }

    /**
     * Название роли
     * @return mixed
     */
    public function getRoleLabel()
    {
        $rolesList = self::getRolesList();

        return $rolesList[$this->role];
    }

    /**
     * Список доступных мастеров
     * @return array
     */
    public static function getWorkersList()
    {
        $workers = User::find()
            ->select(['name' => new Expression('CONCAT(last_name, \' \', first_name)')])
            ->where(['role' => User::ROLE_WORKER])
            ->enabled()
            ->notDeleted()
            ->indexBy('id')
            ->column();

        return $workers;

    }
}
