<?php

namespace app\models;

use Yii;
use \app\models\base\BalanceHistory as BaseBalanceHistory;

/**
 * This is the model class for table "balance_history".
 */
class BalanceHistory extends BaseBalanceHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['value'], 'number'],
            [['clinet', 'currancy'], 'integer'],
            [['AT'], 'required'],
            [['client_type', 'min_date', 'max_date', 'allData', 'client_type'], 'safe'],
            [['why'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 14]
        ]);
    }
	
}
