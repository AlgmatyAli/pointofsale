<?php
 
namespace app\models;
use Yii;
 
use yii\db\ActiveRecord;
 
class User extends ActiveRecord implements \yii\web\IdentityInterface
{

public $file;
public static function tableName() { return 'user'; }
 
   /**
 * @inheritdoc
 */
  public function rules()
  {
    return [
        [['isActive', 'branch'], 'required'],
        [['isActive'], 'string'],
        [['createedDate', 'client'], 'safe'],
        [['branch', 'permission', 'seeCostPrice', 'editSalePrice', 'makeDiscount', 'printPurtchaseInvoice', 'seeOtherBranchQ'], 'integer'],
        [['maxDiscount', 'maxExpenses', 'maxReceipt'], 'number'],
        [['username', 'password', 'phone', 'email', 'path'], 'string', 'max' => 255],
        [['username'], 'unique'],
        [['email'], 'email'],
        [['file'], 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 2 * 1024 * 1024],
    ];
}

/**
 * {@inheritdoc}
 */
public function attributeLabels()
{
    return [
        'id' => Yii::t('app', 'ID'),
        'username' => Yii::t('app', 'Username'),
        'password' => Yii::t('app', 'Password'),
        'isActive' => Yii::t('app', 'Is Active'),
        'createedDate' => Yii::t('app', 'Createed Date'),
        'phone' => Yii::t('app', 'Phone'),
        'email' => Yii::t('app', 'Email'),
        'path' => Yii::t('app', 'Path'),
        'branch' => Yii::t('app', 'Branch'),
        'permission' => Yii::t('app', 'Permission'),
        'seeCostPrice' => Yii::t('app', 'See Cost Price'),
        'editSalePrice' => Yii::t('app', 'Edit Sale Price'),
        'makeDiscount' => Yii::t('app', 'Make Discount'),
        'maxDiscount' => Yii::t('app', 'Max Discount'),
        'maxExpenses' => Yii::t('app', 'Max Expenses'),
        'maxReceipt' => Yii::t('app', 'Max Receipt'),
        'printPurtchaseInvoice' => Yii::t('app', 'Print Purtchase Invoice'),
        'client' => Yii::t('app', 'Client'),
        'seeOtherBranchQ' => Yii::t('app', 'See Other Barnch Q'),
    ];
}


/**
 * @inheritdoc
 * يعيد null إذا لم يوجد المستخدم أو كان موقوفاً (فيُنهى دخوله عند الطلب التالي).
 */
public static function findIdentity($id) {
    $user = static::findOne(['id' => $id]);
    if ($user === null || $user->isActive === 'disabled') {
        return null;
    }
    return $user;
}

/**
 * @inheritdoc
 */
public static function findIdentityByAccessToken($token, $userType = null) {
    if ($token === null || $token === '') {
        return null;
    }
    $user = static::findOne(['accessToken' => $token]);
    if ($user === null || $user->isActive === 'disabled') {
        return null;
    }
    return $user;
}

/**
 * Finds user by username
 *
 * @param  string      $username
 * @return static|null
 */
public static function findByUsername($username) {
    return static::findOne(['username' => $username]);
}

public static function findByUser($username) {
    return static::findOne(['username' => $username]);
}

/**
 * @inheritdoc
 */
public function getId() {
    return $this->id;
}

/**
 * مفتاح الدخول التلقائي (remember me). يُولَّد تلقائياً عند أول طلب إن كان فارغاً.
 * @inheritdoc
 */
public function getAuthKey() {
    if (!$this->hasAttribute('authKey')) {
        return null;
    }
    if (empty($this->authKey) && !$this->isNewRecord) {
        $this->authKey = Yii::$app->security->generateRandomString(32);
        $this->updateAttributes(['authKey' => $this->authKey]);
    }
    return $this->authKey;
}

/**
 * @inheritdoc
 */
public function validateAuthKey($authKey) {
    $key = $this->getAuthKey();
    if (empty($key) || !is_string($authKey) || $authKey === '') {
        return false;
    }
    return hash_equals((string)$key, $authKey);
}

/**
 * يولّد authKey للمستخدمين الجدد.
 * @inheritdoc
 */
public function beforeSave($insert) {
    if (!parent::beforeSave($insert)) {
        return false;
    }
    if ($insert && $this->hasAttribute('authKey') && empty($this->authKey)) {
        $this->authKey = Yii::$app->security->generateRandomString(32);
    }
    return true;
}

/**
 * Validates password.
 * يدعم النظامين: bcrypt/argon (الجديد) و md5 (القديم) حتى يتم ترحيل كل المستخدمين.
 *
 * @param  string  $password password to validate
 * @return boolean if password provided is valid for current user
 */
public function validatePassword($password) {
    $stored = (string)$this->password;
    if ($stored === '' || !is_string($password) || $password === '') {
        return false;
    }
    if (strncmp($stored, '$2y$', 4) === 0 || strncmp($stored, '$argon', 6) === 0) {
        return Yii::$app->security->validatePassword($password, $stored);
    }
    return hash_equals($stored, md5($password));
}

/**
 * يرقّي hash قديم (md5) إلى bcrypt بعد نجاح الدخول بكلمة المرور الصحيحة.
 * لا يفعل شيئاً إن كان الـ hash جديداً أو كان عمود password أصغر من 60 حرفاً.
 */
public function upgradePasswordHash($password) {
    $stored = (string)$this->password;
    if (strncmp($stored, '$2y$', 4) === 0 || strncmp($stored, '$argon', 6) === 0) {
        return;
    }
    $col = static::getTableSchema()->getColumn('password');
    if ($col === null || ($col->size !== null && (int)$col->size < 60)) {
        return;
    }
    $this->setPassword($password);
    $this->updateAttributes(['password' => $this->password]);
}

/**
 * يحوّل كلمة المرور النصية إلى hash آمن (bcrypt). لم تُستخدم بعد في الشاشات.
 * @param string $password
 */
public function setPassword($password) {
    $this->password = Yii::$app->security->generatePasswordHash($password);
}

 /**
     * Gets query for [[Br]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBr()
    {
        return $this->hasOne(Branches::class, ['id' => 'branch']);
    }

    public function getAuth()
    {
        return $this->hasOne(\app\models\AuthItem::class, ['type' => 'permission']);
    }
}
