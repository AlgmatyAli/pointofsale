<?php

namespace app\models;

use Yii;
use \app\models\base\SalesDetailsDeleted as BaseSalesDetailsDeleted;

/**
 * This is the model class for table "salesDetails_deleted".
 */
class SalesDetailsDeleted extends BaseSalesDetailsDeleted
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['id', 'salesId', 'category', 'box'], 'integer'],
            [['salesId', 'category', 'quantity', 'costPrice', 'box'], 'required'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
            [['expire'], 'safe']
        ]);
    }
	
}
