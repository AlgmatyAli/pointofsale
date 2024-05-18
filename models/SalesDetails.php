<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salesDetails".
 *
 * @property int $id
 * @property int $salesId
 * @property int $category
 * @property float $quantity
 * @property float $costPrice
 * @property float|null $salePrice
 * @property int $box
 * @property string|null $expire
 *
 * @property Category $cat
 * @property Sales $sales
 */
class SalesDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $company, $serialNo, $commCode, $class, $client;
    public static function tableName()
    {
        return 'salesDetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['salesId', 'category', 'quantity', 'costPrice', 'box'], 'required'],
            [['salesId', 'type','category', 'box'], 'integer'],
            [['quantity', 'costPrice', 'salePrice', 'original_price'], 'number'],
            [['expire','type','mac_address','serial_number', 'packing', 'waitQnty', 'company', 'serialNo', 'commCode', 'class', 'client'], 'safe'],
            [['category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category' => 'id']],
            [['salesId'], 'exist', 'skipOnError' => true, 'targetClass' => Sales::className(), 'targetAttribute' => ['salesId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'salesId' => Yii::t('app', 'Sales ID'),
            'category' => Yii::t('app', 'Cat ID'),
            'quantity' => Yii::t('app', 'quantity'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'serial_number' => Yii::t('app', 'Serial Number'),
            'box' => Yii::t('app', 'Box'),
            'mac_address' => Yii::t('app', 'Mac Address'),
            'original_price'=>Yii::t('app','Original Price'),
            'expire' => Yii::t('app', 'Expire'),
            'packing' => Yii::t('app', 'Packing'),
            'waitQnty' => Yii::t('app', 'Wait Qnty')
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }
    public function getCategory0()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }

    /**
     * Gets query for [[Sales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasOne(Sales::className(), ['id' => 'salesId']);
    }
    public function getPrice()
    {
        return $this->hasOne(Totalinventory::className(), ['id' => 'category']);
    }

    public function getPrices()
    {
        return $this->hasOne(Prices::className(), ['id' => 'category']);
    }
    
}
