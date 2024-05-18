<?php

namespace app\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "sales_deleted".
 *
 * @property integer $id
 * @property integer $billId
 * @property string $at
 * @property integer $clinet
 * @property integer $payWay
 * @property integer $branch
 * @property string $total
 * @property string $paid
 * @property string $notes
 * @property string $path
 * @property integer $type
 * @property string $deleviryAt
 * @property integer $carpenter
 * @property integer $upholstered
 * @property integer $paintId
 * @property integer $deleviryId
 * @property integer $user_insert
 * @property string $created_at
 * @property integer $user_update
 * @property string $update_at
 *
 * @property \app\models\SalesDetailsDeleted[] $salesDetailsDeleteds
 */
class SalesDeleted extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'salesDetailsDeleteds'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'billId', 'at', 'clinet', 'payWay', 'branch', 'total', 'type', 'user_insert', 'created_at'], 'required'],
            [['id', 'billId', 'clinet', 'payWay', 'branch', 'type', 'carpenter', 'upholstered', 'paintId', 'deleviryId', 'user_insert', 'user_update'], 'integer'],
            [['at', 'deleviryAt', 'created_at', 'update_at'], 'safe'],
            [['total', 'paid'], 'number'],
            [['notes', 'path'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales_deleted';
    }

    /**
     * @inheritdoc
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
            'paid' => Yii::t('app', 'Paid'),
            'notes' => Yii::t('app', 'Notes'),
            'path' => Yii::t('app', 'Path'),
            'type' => Yii::t('app', 'Type'),
            'deleviryAt' => Yii::t('app', 'Deleviry At'),
            'carpenter' => Yii::t('app', 'Carpenter'),
            'upholstered' => Yii::t('app', 'Upholstered'),
            'paintId' => Yii::t('app', 'Paint ID'),
            'deleviryId' => Yii::t('app', 'Deleviry ID'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetailsDeleteds()
    {
        return $this->hasMany(\app\models\SalesDetailsDeleted::className(), ['salesId' => 'id']);
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
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\SalesDeletedQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\SalesDeletedQuery(get_called_class());
    }
}
