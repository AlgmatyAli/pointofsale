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
            [['value'], 'number'],
            [['clinet', 'currancy'], 'integer']
        ]);
    }
	
}
