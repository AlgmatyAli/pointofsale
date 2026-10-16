<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->isLockedOut()) {
            $this->addError($attribute, Yii::t('app', 'Too many failed attempts. Please try again in a few minutes.'));
            return;
        }

        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->registerFailure();
            // رسالة موحّدة: لا تكشف هل اسم المستخدم موجود أم لا
            $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            return;
        }

        // لا تُعرض رسالة الإيقاف إلا بعد التحقق من كلمة المرور الصحيحة
        if ($user->isActive == 'disabled') {
            $this->addError($attribute, 'المستخدم موقوف');
            return;
        }

        $this->clearFailures();
        $user->upgradePasswordHash($this->password);
    }

    const MAX_ATTEMPTS = 5;
    const LOCK_SECONDS = 300;

    private function attemptsKey()
    {
        return 'login-attempts:' . md5(mb_strtolower((string)$this->username) . '|' . Yii::$app->request->userIP);
    }

    private function isLockedOut()
    {
        $cache = Yii::$app->cache;
        return $cache && (int)$cache->get($this->attemptsKey()) >= self::MAX_ATTEMPTS;
    }

    private function registerFailure()
    {
        $cache = Yii::$app->cache;
        if ($cache) {
            $key = $this->attemptsKey();
            $cache->set($key, (int)$cache->get($key) + 1, self::LOCK_SECONDS);
        }
    }

    private function clearFailures()
    {
        $cache = Yii::$app->cache;
        if ($cache) {
            $cache->delete($this->attemptsKey());
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
