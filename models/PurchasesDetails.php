<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "purchasesDetails".
 *
 * @property int $id
 * @property int $PurchasesId
 * @property int $category
 * @property int $quantity
 * @property float $costPrice
 * @property string|null $totalCost
 * @property float|null $salePrice
 * @property float|null $salePrice_
 * @property float|null $salePrice_2
 * @property float|null $salePrice_3
 * @property int $box
 * @property string|null $expire
 *
 * @property Category $category0
 * @property Purchases $purchases
 
*/
class PurchasesDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $item_total;
    public static function tableName()
    {
        return 'purchasesDetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PurchasesId', 'category', 'quantity', 'costPrice', 'box'], 'required'],
            [['PurchasesId', 'category', 'quantity', 'box'], 'integer'],
            [['costPrice', 'totalCost', 'salePrice', 'salePrice_', 'salePrice_2', 'salePrice_3'], 'number'],
            [['expire'], 'safe'],
            [['totalCost'], 'string', 'max' => 255],
            [['category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category' => 'id']],
            [['PurchasesId'], 'exist', 'skipOnError' => true, 'targetClass' => Purchases::class, 'targetAttribute' => ['PurchasesId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'PurchasesId' => Yii::t('app', 'Purchases ID'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
            'quantity' => Yii::t('app', 'quantity'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'totalCost' => Yii::t('app', 'Total Cost'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'box' => Yii::t('app', 'Box'),
            'clinet' => Yii::t('app', 'Clinet'),
            'expire' => Yii::t('app', 'Expire'),
            'salePrice_' => Yii::t('app', 'Sale Price_'), 
            'salePrice_2' => Yii::t('app', 'Sale Price 2'), 
            'salePrice_3' => Yii::t('app', 'Sale Price 3'), 
        ];
    }

    /**
     * Gets query for [[Category0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory0()
    {
        return $this->hasOne(Category::class, ['id' => 'category']);
    }

    /**
     * Gets query for [[Purchases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchases()
    {
        return $this->hasOne(Purchases::class, ['id' => 'PurchasesId']);
    }

   

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'clinet'])->via('purchases');
    }
}
