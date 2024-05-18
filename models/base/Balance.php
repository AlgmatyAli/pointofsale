<?php

namespace app\models\base;

use app\models\Client;
use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "balance".
 *
 * @property string $value
 * @property integer $clinet
 * @property integer $currancy
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
            [['value'], 'number'],
            [['clinet', 'currancy'], 'integer']
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
            'value' => Yii::t('app', 'Value'),
            'clinet' => Yii::t('app', 'Clinet'),
            'currancy' => Yii::t('app', 'Currancy'),
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


    /**
     * @inheritdoc
     * @return \app\models\BalanceQuery the active query used by this AR class.
     *
     */

    public function getClient0()
    {
        return $this->hasOne(Client::className(), ['id' => 'clinet']);
    }

    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::className(), ['id' => 'currancy']);
    }
    public static function find()
    {
        return new \app\models\BalanceQuery(get_called_class());
    }
}
