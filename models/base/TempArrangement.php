<?php

namespace app\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "temp_arrangement".
 *
 * @property integer $id
 * @property integer $category
 * @property double $quantity
 * @property integer $box
 * @property integer $type
 * @property integer $state
 * @property string $expire
 * @property integer $branch
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property \app\models\User $createdBy
 * @property \app\models\User $updatedBy
 * @property \app\models\Category $category0
 * @property \app\models\Branches $branch0
 */
class TempArrangement extends \yii\db\ActiveRecord
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
            'category0',
            'branch0'
        ];
    }

    /**
     * @inheritdoc
     */
    public $realQuantity;
    public function rules()
    {
        return [
            [['category', 'quantity', 'branch'], 'required'],
            [['category', 'box', 'type', 'state', 'branch', 'created_by', 'updated_by', 'stockTaking'], 'integer'],
            [['quantity', 'realQuantity'], 'number'],
            [['expire', 'created_at', 'updated_at', 'type'], 'safe'],
            // [['lock'], 'default', 'value' => '0'],
            // [['lock'], 'mootensai\components\OptimisticLockValidator']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'temp_arrangement';
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
            'quantity' => Yii::t('app', 'Quantity'),
            'box' => Yii::t('app', 'Box'),
            'type' => Yii::t('app', 'Type'),
            'state' => Yii::t('app', 'State'),
            'expire' => Yii::t('app', 'Expire'),
            'branch' => Yii::t('app', 'Branch'),
            'realQuantity ' => Yii::t('app', 'Real Quantity'),
            'stockTaking' => Yii::t('app', 'Stock Taking'), 
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
     * @return \yii\db\ActiveQuery
     */
    public function getBranch0()
    {
        return $this->hasOne(\app\models\Branches::className(), ['id' => 'branch']);
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
     * @return \app\models\TempArrangementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\TempArrangementQuery(get_called_class());
    }
}
