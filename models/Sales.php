<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sales".
 *
 * @property int $id
 * @property int $billId
 * @property string $at
 * @property int $clinet
 * @property int $payWay
 * @property int $branch
 * @property float $total
 * @property float|null $disscount
 * @property float|null $paid
 * @property string|null $notes
 * @property string|null $path
 * @property int $type
 * @property string|null $deleviryAt
 * @property string|null $deserving
 * @property int|null $deleviried
 * @property int $currancy
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property int|null $wholesale
 * @property int|null $client
 * @property int|null $waitQnty
 *
 * @property Branches $branch0
 * @property Client $clinet0
 * @property Currancy $currancy0
 * @property Salesdetails[] $salesdetails
 * @property User $userInsert
 * @property User $userUpdate
 */
class Sales extends \yii\db\ActiveRecord
{
    public $file;
    public $phone,$net,$category, $min_date, $max_date;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['disscount', 'paid', 'notes', 'path', 'deleviryAt', 'deserving', 'deleviried', 'user_update', 'update_at'], 'default', 'value' => null],
            [['currancy'], 'default', 'value' => 1],
            [['wholesale'], 'default', 'value' => 0],
            [['billId', 'at', 'clinet', 'payWay', 'branch', 'total', 'type', 'user_insert'], 'required'],
            [['billId', 'clinet', 'payWay', 'branch', 'type', 'deleviried', 'currancy', 'user_insert', 'user_update', 'wholesale'], 'integer'],
            [['at', 'deleviryAt', 'deserving', 'created_at', 'update_at', 'phone', 'net', 'category', 'min_date', 'max_date'], 'safe'],
            [['total', 'disscount', 'paid'], 'number'],
            [['notes', 'path'], 'string', 'max' => 255],
            [['branch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::class, 'targetAttribute' => ['branch' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_update' => 'id']],
            [['clinet'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['clinet' => 'id']],
            [['currancy'], 'exist', 'skipOnError' => true, 'targetClass' => Currancy::class, 'targetAttribute' => ['currancy' => 'id']],
            [['file'], 'file']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'billId' => Yii::t('app', 'Bill ID'),
            'at' => Yii::t('app', 'At'),
            'clinet' => Yii::t('app', 'Clinet'),
            'payWay' => Yii::t('app', 'Pay Way'),
            'branch' => Yii::t('app', 'Branch'),
            'total' => Yii::t('app', 'Total'),
            'disscount' => Yii::t('app', 'Disscount'),
            'paid' => Yii::t('app', 'Paid'),
            'notes' => Yii::t('app', 'Notes'),
            'path' => Yii::t('app', 'Path'),
            'type' => Yii::t('app', 'Type'),
            'deleviryAt' => Yii::t('app', 'Deleviry At'),
            'deserving' => Yii::t('app', 'Deserving'),
            'deleviried' => Yii::t('app', 'Deleviried'),
            'currancy' => Yii::t('app', 'Currancy'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'wholesale' => Yii::t('app', 'Wholesale'),
            'min_date' => Yii::t('app', 'Min Date'),
            'max_date' => Yii::t('app', 'Max Date'),
            'net' => Yii::t('app', 'Net'),
            'phone' => Yii::t('app', 'Phone'),
        ];
    }

    /**
     * Gets query for [[Branch0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::class, ['id' => 'branch']);
    }

    /**
     * Gets query for [[Clinet0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getC()
    {
        return $this->hasOne(Client::class, ['id' => 'clinet']);
    }

    /**
     * Gets query for [[Currancy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::class, ['id' => 'currancy']);
    }

    /**
     * Gets query for [[Salesdetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::class, ['salesId' => 'id']);
    }

    /**
     * Gets query for [[UserInsert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserInsert()
    {
        return $this->hasOne(User::class, ['id' => 'user_insert']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'user_update']);
    }

}
