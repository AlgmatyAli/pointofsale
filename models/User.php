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


public static function findIdentity($id) {
    $user = self::find()
            ->where([
                "id" => $id
            ])
            ->one();
    return new static($user);
}
 
/**
 * @inheritdoc
 */
public static function findIdentityByAccessToken($token, $userType = null) {
 
    $user = self::find()
            ->where(["accessToken" => $token])
            ->one();
    if (!count($user)) {
        return null;
    }
    return new static($user);
}
 
/**
 * Finds user by username
 *
 * @param  string      $username
 * @return static|null
 */
public static function findByUsername($username) {
    $user = self::find()
            ->where([
                "username" => $username
            ])
            ->one();
    return new static($user);
}
 
public static function findByUser($username) {
    $user = self::find()
            ->where([
                "username" => $username
            ])
            ->one();
    if (!count($user)) {
        return null;
    }
    return $user;
}
 
/**
 * @inheritdoc
 */
public function getId() {
    return $this->id;
}
 
/**
 * @inheritdoc
 */
public function getAuthKey() {
    //return $this->authKey;
}
 
/**
 * @inheritdoc
 */
public function validateAuthKey($authKey) {
    return $this->authKey === $authKey;
}
 
/**
 * Validates password
 *
 * @param  string  $password password to validate
 * @return boolean if password provided is valid for current user
 */
public function validatePassword($password) {
   // return md5($this->password) ===  ($password);
    return $this->password === md5($password);
}

 /**
     * Gets query for [[Br]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBr()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch']);
    }

    public function getAuth()
    {
        return $this->hasOne(\app\models\AuthItem::className(), ['type' => 'permission']);
    }

    public function actionSubscriber()
    {
    $subscriber_model  = new Subscriber();

    $request = Yii::$app->request;
    if($request->isAjax && $subscriber_model->load($request->post()) && $subscriber_model->validate() && $subscriber_model->save())
    {
        // return something here
    }
    }
}