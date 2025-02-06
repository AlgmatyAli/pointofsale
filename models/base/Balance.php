<?php

namespace app\models\base;

use Yii;

/**
 * This is the base model class for table "balance".
 *
 * @property integer $id
 * @property string $name
 * @property double $credt
 * @property string $phone
 * @property integer $type
 * @property string $deserving
 * @property integer $currency
 */
class Balance extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'currency'], 'integer'],
            [['credt'], 'number'],
            [['deserving'], 'safe'],
            [['name', 'phone'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'credt' => Yii::t('app', 'Credt'),
            'phone' => Yii::t('app', 'Phone'),
            'type' => Yii::t('app', 'Type'),
            'deserving' => Yii::t('app', 'Deserving'),
            'currency' => Yii::t('app', 'Currency'),
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\BalanceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\BalanceQuery(get_called_class());
    }

    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::class, ['id' => 'currency']);
    }
}
