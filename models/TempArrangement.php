<?php

namespace app\models;

use Yii;
use \app\models\base\TempArrangement as BaseTempArrangement;

/**
 * This is the model class for table "temp_arrangement".
 */
class TempArrangement extends BaseTempArrangement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['category', 'quantity', 'branch'], 'required'],
            [['category', 'box', 'type', 'state', 'branch', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'realQuantity'], 'number'],
            [['expire', 'created_at', 'updated_at', 'type'], 'safe'],
            // [['lock'], 'default', 'value' => '0'],
            // [['lock'], 'mootensai\components\OptimisticLockValidator']
        ]);
    }
	
}
