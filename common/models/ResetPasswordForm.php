<?php
namespace common\models;

use common\models\ar\User;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\web\BadRequestHttpException;

/**
 * Reset Password form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $passwordRepeat;

    /** @var User  */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidParamException if token is empty or not valid
     * @throws BadRequestHttpException if token is empty or not valid
     */

    public function __construct($token, $config = [])
    {
        try{
            if (empty($token) || !is_string($token)) {
                throw new InvalidParamException('token не может быть пустым');
            }
            $this->_user = User::findByPasswordResetToken($token);
            if (!$this->_user) {
                throw new InvalidParamException('Неправильный token');
            }
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'passwordRepeat'], 'required'],
            [['password', 'passwordRepeat'], 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }


    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'password' => 'Пароль',
            'passwordRepeat' => 'Повт. пароль',
        ]);
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */

    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }
}
