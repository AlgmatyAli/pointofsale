<?php

namespace app\models;

use Yii;
use \app\models\base\Balance as BaseBalance;

/**
 * This is the model class for table "balance".
 */
class Balance extends BaseBalance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['id', 'type', 'currency'], 'integer'],
            [['credt'], 'number'],
            [['deserving'], 'safe'],
            [['name', 'phone'], 'string', 'max' => 255]
        ]);
    }
	
    /**
     * @inheritdoc
     */
    public function attributeHints()
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
}
