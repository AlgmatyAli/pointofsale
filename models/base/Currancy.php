<?php

namespace app\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "currancy".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $user_insert
 * @property string $created_at
 * @property integer $user_update
 * @property string $update_at
 * @property integer $purchases
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property \app\models\User $userInsert
 * @property \app\models\User $userUpdate
 * @property \app\models\User $createdBy
 * @property \app\models\Purchases $purchases0
 * @property \app\models\User $updatedBy
 * @property \app\models\Receipt[] $receipts
 */
class Currancy extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'userInsert',
            'userUpdate',
            'createdBy',
            'purchases0',
            'updatedBy',
            'receipts'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code', 'created_at'], 'required'],
            [['user_insert', 'user_update', 'purchases', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currancy';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'purchases' => Yii::t('app', 'Purchases'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInsert()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'user_insert']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'user_update']);
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
    public function getPurchases0()
    {
        return $this->hasOne(\app\models\Purchases::className(), ['id' => 'purchases']);
    }
    public function getCompanyInfos() 
    { 
        return $this->hasMany(\app\models\CompanyInfo::className(), ['currancy' => 'id']); 
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
    public function getReceipts()
    {
        return $this->hasMany(\app\models\Receipt::className(), ['currancy' => 'id']);
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
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\CurrancyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\CurrancyQuery(get_called_class());
    }
}
