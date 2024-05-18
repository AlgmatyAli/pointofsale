<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $address
 * @property string|null $email
 * @property string|null $balance
 * @property int $type
 * @property int $branch
 * @property int $user_insert
 * @property int|null $user_update
 * @property string $created_at
 * @property string|null $update_at
 *
 * @property User $userInsert
 * @property User $userUpdate
 * @property Purchases[] $purchases
 * @property Receipt[] $receipts
 * @property Sales[] $sales
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $sumwared, $sumsader, $count;
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'branch', 'user_insert', 'created_at'], 'required'],
            [['type', 'branch', 'user_insert', 'user_update'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['debt'], 'number'],
            [['name', 'phone', 'mobile', 'address', 'email', 'balance'], 'string', 'max' => 255],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'mobile' => Yii::t('app', 'Mobile'),
            'address' => Yii::t('app', 'Address'),
            'email' => Yii::t('app', 'Email'),
            'balance' => Yii::t('app', 'Balance'),
            'type' => Yii::t('app', 'Type'),
            'branch' => Yii::t('app', 'Br ID'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'user_update' => Yii::t('app', 'User Update'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
            'debt' => Yii::t('app', 'Debt'),
        ];
    }

    /**
     * Gets query for [[UserInsert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserInsert()
    {
        return $this->hasOne(User::className(), ['id' => 'user_insert']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(User::className(), ['id' => 'user_update']);
    }

    /**
     * Gets query for [[Purchases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchases()
    {
        return $this->hasMany(Purchases::className(), ['clinet' => 'id']);
    }

    /**
     * Gets query for [[Receipts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceipts()
    {
        return $this->hasMany(Receipt::className(), ['clinet' => 'id']);
    }
    public function getDebit()
    {
        return $this->hasMany(Receipt::className(), ['clinet' => 'id'])
        ->andwhere(['type' => 2])
        ;
    }
    public function getCredit()
    {
        return $this->hasMany(Receipt::className(), ['clinet' => 'id'])
        ->andwhere(['type' => 1])
        ;
    }

    /**
     * Gets query for [[Sales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sales::className(), ['clinet' => 'id'])
        ->andwhere(['type' => 1])
        ;
    }

    public function getInitsales()
    {
        return $this->hasMany(Sales::className(), ['clinet' => 'id'])
        ->andwhere(['type' => 3])
        ;
    }
    
}
