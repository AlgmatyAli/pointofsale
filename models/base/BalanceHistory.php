<?php

namespace app\models\base;

use app\models\Client;
use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "balance_history".
 *
 * @property string $value
 * @property integer $clinet
 * @property integer $currancy
 * @property string $at
 * @property string $why
 * @property string $type
 */
class BalanceHistory extends \yii\db\ActiveRecord
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
    public $min_date, $max_date,  $allData;
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['clinet', 'currancy'], 'integer'],
            [['AT'], 'required'],
            [['min_date', 'max_date', 'allData', 'client_type'], 'safe'],
            [['why'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 14]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_history';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => Yii::t('app', 'Value'),
            'clinet' => Yii::t('app', 'Clinet'),
            'currancy' => Yii::t('app', 'Currancy'),
            'AT' => Yii::t('app', 'AT'),
            'why' => Yii::t('app', 'Why'),
            'type' => Yii::t('app', 'Type'),
            'min_date' => Yii::t('app', 'Min Date'),
            'max_date' => Yii::t('app', 'Max Date'),
            'allData' => Yii::t('app', 'All Date'),
            'client_type' => Yii::t('app', 'Client Type'),
        ];
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
    public function getClient0()
    {
        return $this->hasOne(Client::className(), ['id' => 'clinet']);
    }

    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::className(), ['id' => 'currancy']);
    }

    /**
     * @inheritdoc
     * @return \app\models\BalanceHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\BalanceHistoryQuery(get_called_class());
    }
}
