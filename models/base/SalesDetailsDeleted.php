<?php

namespace app\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "salesDetails_deleted".
 *
 * @property integer $id
 * @property integer $salesId
 * @property integer $category
 * @property double $quantity
 * @property string $costPrice
 * @property string $salePrice
 * @property integer $box
 * @property string $expire
 *
 * @property \app\models\SalesDeleted $sales
 */
class SalesDetailsDeleted extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'sales'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'salesId', 'category', 'box'], 'integer'],
            [['salesId', 'category', 'quantity', 'costPrice', 'box'], 'required'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
            [['expire'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salesDetails_deleted';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'salesId' => Yii::t('app', 'Sales ID'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'box' => Yii::t('app', 'Box'),
            'expire' => Yii::t('app', 'Expire'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasOne(\app\models\SalesDeleted::className(), ['id' => 'salesId']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    // public function behaviors()
    // {
    //     return [
    //         'timestamp' => [
    //             'class' => TimestampBehavior::className(),
    //             'createdAtAttribute' => 'created_at',
    //             'updatedAtAttribute' => 'updated_at',
    //             'value' => new \yii\db\Expression('NOW()'),
    //         ],
    //         'blameable' => [
    //             'class' => BlameableBehavior::className(),
    //             'createdByAttribute' => 'created_by',
    //             'updatedByAttribute' => 'updated_by',
    //         ],
    //     ];
    // }


    /**
     * @inheritdoc
     * @return \app\models\SalesDetailsDeletedQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\SalesDetailsDeletedQuery(get_called_class());
    }
}
