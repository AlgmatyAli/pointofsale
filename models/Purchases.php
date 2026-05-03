<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "purchases".
 *
 * @property int $id
 * @property int $billId
 * @property int $clinet
 * @property string $at
 * @property int $payWay
 * @property string $clientBill
 * @property int $BuyFor
 * @property int $branch
 * @property float $total
 * @property float|null $paid
 * @property string|null $notes
 * @property string|null $path
 * @property int $type
 * @property string|null $totalCost
 * @property string|null $standBy
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property string|null $currancy
 * @property string|null $total_currancy
 * @property Branches $br
 * @property Client $c
 * @property User $userInsert
 * @property User $userUpdate
 * @property PurchasesDetails[] $purchasesDetails
 */
class Purchases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $file, $changeSalePrice;

    public static function tableName()
    {
        return 'purchases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'billId',
                'clinet',
                'at',
                'payWay',
                'clientBill',
                'BuyFor',
                'branch',
                'total',
                'type',
                'user_insert',
                'created_at',
                'total',
                'paid',
                'payment_type'
            ], 'required'],
            [[
                'billId',
                'clinet',
                'payWay',
                'BuyFor',
                'branch',
                'type',
                'currancy',
                'user_insert',
                'user_update',
                'shippingType',
                'payment_type'
            ], 'integer'],
            [['at', 'created_at', 'update_at', 'dateOfArrival', 'changeSalePrice'], 'safe'],
            [['total', 'paid', 'total_currancy',], 'number'],
            [['clientBill', 'notes', 'path', 'totalCost', 'standBy'], 'string', 'max' => 255],
            [['branch'], 'exist', 'skipOnError' => true, 'targetClass' =>
            Branches::class, 'targetAttribute' => ['branch' => 'id']],
            [['clinet'], 'exist', 'skipOnError' => true, 'targetClass' =>
            Client::class, 'targetAttribute' => ['clinet' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' =>
            User::class, 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' =>
            User::class, 'targetAttribute' => ['user_update' => 'id']],
            [['shippingType'], 'exist', 'skipOnError' => true, 'targetClass' =>
            ShippingType::class, 'targetAttribute' => ['dateOfArrival' => 'id']],
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
            'clinet' => Yii::t('app', 'C ID'),
            'at' => Yii::t('app', 'At'),
            'payWay' => Yii::t('app', 'Pay Way'),
            'clientBill' => Yii::t('app', 'Client Bill'),
            'BuyFor' => Yii::t('app', 'Buy For'),
            'branch' => Yii::t('app', 'Br ID'),
            'total' => Yii::t('app', 'Total'),
            'paid' => Yii::t('app', 'Paid'),
            'notes' => Yii::t('app', 'Notes'),
            'path' => Yii::t('app', 'Path'),
            'type' => Yii::t('app', 'Type'),
            'totalCost' => Yii::t('app', 'Total Cost'),
            'standBy' => Yii::t('app', 'Stand By'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'item_total' => Yii::t('app', 'Item Total'),
            'total_currancy' => Yii::t('app', 'Total Currancy'),
            'currancy' => Yii::t('app', 'Currancy'),
            'shippingType' => Yii::t('app', 'Shipping Type'),
            'dateOfArrival' => Yii::t('app', 'Date Of Arrival'),
            'changeSalePrice' => Yii::t('app', 'Change Sale Price'),
            'payment_type' => Yii::t('app', 'Payment Type'),

        ];
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

    /**
     * Gets query for [[C]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getC()
    {
        return $this->hasOne(Client::class, ['id' => 'clinet']);
    }

    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::class, ['id' => 'currancy']);
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

    public function getShippingType0()
    {
        return $this->hasOne(ShippingType::class, ['id' => 'shippingType']);
    }

    /**
     * Gets query for [[PurchasesDetails]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getPurchasesDetails()
    {
        return $this->hasMany(PurchasesDetails::class, ['PurchasesId' => 'id']);
    }

    public function getPaymentType0()
    {
        return $this->hasOne(PaymentTypes::class, ['id' => 'payment_type']);
    }
}
