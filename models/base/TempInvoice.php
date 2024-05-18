<?php

namespace app\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "temp_invoice".
 *
 * @property integer $id
 * @property integer $invoice_number
 * @property integer $category
 * @property string $serial_number
 * @property double $quantity
 * @property double $costPrice
 * @property double $salePrice
 * @property integer $box
 * @property integer $state
 * @property string $expire
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property \app\models\User $createdBy
 * @property \app\models\User $updatedBy
 * @property \app\models\Category $category0
 */
class TempInvoice extends \yii\db\ActiveRecord
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
    public $cat, $kind;
    public $checkboxValues;
    public $textInputValues;
    public function rules()
    {
        return [
            [['invoice_number','type', 'category', 'box', 'state', 'created_by', 'updated_by', 'cat', 'kind'], 'integer'],
            //[['category'], 'required'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
            [['expire', 'mac_address','type','created_at', 'updated_at', 'waitQnty'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'temp_invoice';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'invoice_number' => Yii::t('app', 'Invoice Number'),
            'category' => Yii::t('app', 'Category'),
            'serial_number' => Yii::t('app', 'Serial Number'),
            'quantity' => Yii::t('app', 'Quantity'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'box' => Yii::t('app', 'Box'),
            'state' => Yii::t('app', 'State'),
            'expire' => Yii::t('app', 'Expire'),
            'mac_address' => Yii::t('app', 'Mac Address'),
            'kind' => Yii::t('app', 'Category'),
            'cat' => Yii::t('app', 'ID'),
            'waitQnty' => Yii::t('app', 'Wait Qnty'),
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
    
    public function getPrice()
    {
        return $this->hasOne(\app\models\Prices::className(), ['category' => 'category']);
    }

    // public function getInventory()
    // {
    //     return $this->hasMany(\app\models\Inventory::className(), ['id' => 'category'])->sum('quantity') ;
    // }
    
    
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
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\TempInvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\TempInvoiceQuery(get_called_class());
    }
}
