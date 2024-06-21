<?php

namespace app\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "temp_invoice_purchase".
 *
 * @property integer $id
 * @property integer $category
 * @property double $quantity
 * @property string $costPrice
 * @property string $state
 * @property string $salePrice
 * @property integer $box
 * @property string $expire
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property double $salePrice_
 * @property double $salePrice_2
 * @property double $salePrice_3
 * @property double $costTotal
 *
 * @property \app\models\User $createdBy
 * @property \app\models\User $updatedBy
 * @property \app\models\Category $category0
 */
class TempInvoicePurchase extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'createdBy',
            'updatedBy',
            'category0'
        ];
    }

    /**
     * @inheritdoc
     */
    public $rate, $totalInvoice, $totalCost, $profit, $derhamRate; 
    public $file, $path;
    public function rules()
    {
        return [
            [['category', 'quantity', 'salePrice', 'salePrice_', 'salePrice_2', 'salePrice_3', 'costPrice'], 'required'],
            [['category', 'box', 'created_by', 'updated_by'], 'integer'],
            [['id', 'costTotal', 'quantity', 'costPrice', 'state', 'salePrice', 'salePrice_',
            'salePrice_2', 'salePrice_3', 'rate', 'totalInvoice', 'totalCost', 'profit', 'derhamRate'], 'number'],
            [['expire', 'created_at', 'updated_at', 'path'], 'safe'],
            [['file'], 'file']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'temp_invoice_purchase';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'state' => Yii::t('app', 'Total Price'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'salePrice_' => Yii::t('app', 'Sale Price_'),
            'box' => Yii::t('app', 'Box'),
            'expire' => Yii::t('app', 'Expire'),
            'rate' => Yii::t('app', 'Rate'),
            'totalInvoice' => Yii::t('app', 'Total Invoice'),
            'totalCost' => Yii::t('app', 'Total Cost'),
            'costTotal' => Yii::t('app', 'Cost Total'),
            'profit' => Yii::t('app', 'Profit'),
            'state' => Yii::t('app', 'State'),
            'salePrice_2' => Yii::t('app', 'Sale Price 2'),
            'salePrice_3' => Yii::t('app', 'Sale Price 3'),
            'derhamRate' => Yii::t('app', 'Derham Rate'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'created_by']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'updated_by']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory0()
    {
        return $this->hasOne(\app\models\Category::className(), ['id' => 'category']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            // 'uuid' => [
            //     'class' => UUIDBehavior::className(),
            //     'column' => 'id',
            // ],
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\TempInvoicePurchaseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\TempInvoicePurchaseQuery(get_called_class());
    }
}
