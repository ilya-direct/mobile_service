<?php

namespace api\components\app;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\rbac\CheckAccessInterface;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;
use common\models\ar\UserToken;

class User extends \yii\web\User
{
    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass;
    
    
    public $authTimeout;
    
    /**
     * @var CheckAccessInterface The access checker to use for checking access.
     * If not set the application auth manager will be used.
     * @since 2.0.9
     */
    public $accessChecker;
    
    /**
     * @var array Cached assess
     */
    private $_access = [];
    
    private $_identity = false;
    
    /**
     * Initializes the application component.
     */
    public function init()
    {
        parent::init();
        
        if ($this->identityClass === null) {
            throw new InvalidConfigException('User::identityClass must be set.');
        }
    }
    
    public function getIdentity($autoRenew = true)
    {
        if ($this->_identity === false) {
            $this->_identity = null;
            $this->renewAuthStatus();
        }
        
        return $this->_identity;
    }
    
    public function setIdentity($identity)
    {
        if ($identity instanceof IdentityInterface) {
            $this->_identity = $identity;
            $this->_access = [];
        } elseif ($identity === null) {
            $this->_identity = null;
        } else {
            throw new InvalidValueException('The identity object must implement IdentityInterface.');
        }
    }
    
    public function login(IdentityInterface $identity, $duration = 0)
    {
        $this->setIdentity($identity);
        $id = $identity->getId();
        $ip = Yii::$app->getRequest()->getUserIP();
        Yii::info("User '$id' logged in from $ip", __METHOD__);
        
        return !$this->getIsGuest();
    }
    
    public function logout($destroySession = true)
    {
        /** @var IdentityInterface|null $identity */
        $identity = $this->getIdentity();
        if ($identity !== null) {
            $this->setIdentity(null);
            $id = $identity->getId();
            $ip = Yii::$app->getRequest()->getUserIP();
            Yii::info("User '$id' logged out from $ip.", __METHOD__);
            UserToken::destroy(Yii::$app->request->headers['Authorization']);
        }
        
        return $this->getIsGuest();
    }
    
    /**
     * Returns a value indicating whether the user is a guest (not authenticated).
     * @return boolean whether the current user is a guest.
     * @see getIdentity()
     */
    public function getIsGuest()
    {
        return $this->getIdentity() === null;
    }
    
    /**
     * Returns a value that uniquely represents the user.
     * @return string|integer the unique identifier for the user. If `null`, it means the user is a guest.
     * @see getIdentity()
     */
    public function getId()
    {
        /** @var IdentityInterface $identity */
        $identity = $this->getIdentity();
        
        return $identity !== null ? $identity->getId() : null;
    }

    /**
     * Updates the authentication status using the information from session and cookie.
     *
     * This method will try to determine the user identity using the [[idParam]] session variable.
     *
     * If [[authTimeout]] is set, this method will refresh the timer.
     *
     * If the user identity cannot be determined by session, this method will try to [[loginByCookie()|login by cookie]]
     * if [[enableAutoLogin]] is true.
     */
    protected function renewAuthStatus()
    {
        $authValue = Yii::$app->request->headers['Authorization'];
        /** @var UserToken $tokenObject */
        $tokenObject = UserToken::findOne(['value' => $authValue]);
        
        if ($tokenObject === null) {
            $identity = null;
        } else {
            /* @var $class IdentityInterface */
            $class = $this->identityClass;
            $identity = $class::findIdentity($tokenObject->user_id);
        }
        
        $this->setIdentity($identity);
        
        if ($identity !== null) {
            $expire = strtotime($tokenObject->expire_date);
            
            if ($expire < time()) {
                $this->logout();
            } else {
                $tokenObject->expire_date =  date('Y-m-d H:i:s', time() + $this->authTimeout);
                $tokenObject->save(false);
            }
        }
    }
    
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if ($allowCaching && empty($params) && isset($this->_access[$permissionName])) {
            return $this->_access[$permissionName];
        }
        if (($accessChecker = $this->getAccessChecker()) === null) {
            return false;
        }
        $access = $accessChecker->checkAccess($this->getId(), $permissionName, $params);
        if ($allowCaching && empty($params)) {
            $this->_access[$permissionName] = $access;
        }
        
        return $access;
    }
    
    /**
     * Returns the access checker used for checking access.
     * @return CheckAccessInterface
     * @since 2.0.9
     */
    protected function getAccessChecker()
    {
        return $this->accessChecker !== null ? $this->accessChecker : Yii::$app->authManager;
    }
    
    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        throw new UnauthorizedHttpException('Login Required');
    }
}
