<?php

namespace app\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "temp_back_sales".
 *
 * @property integer $id
 * @property integer $category
 * @property string $serial_number
 * @property double $quantity
 * @property string $costPrice
 * @property string $salePrice
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
class TempBackSales extends \yii\db\ActiveRecord
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
    public function rules()
    {
        return [
            [['category', 'quantity', 'salePrice'], 'required'],
            [['category', 'box', 'state', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
            [['expire', 'created_at', 'updated_at'], 'safe'],
            [['serial_number'], 'string', 'max' => 255],
           
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'temp_back_sales';
    }

    /**
     *
     * @return string
     * overwrite function optimisticLock
     * return string name of field are used to stored optimistic lock
     *
     */
    // public function optimisticLock() {
    //     return 'lock';
    // }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'serial_number' => Yii::t('app', 'Serial Number'),
            'quantity' => Yii::t('app', 'Quantity'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'box' => Yii::t('app', 'Box'),
            'state' => Yii::t('app', 'State'),
            'expire' => Yii::t('app', 'Expire'),
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
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
            'uuid' => [
                'class' => UUIDBehavior::className(),
                'column' => 'id',
            ],
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\TempBackSalesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\TempBackSalesQuery(get_called_class());
    }
}
