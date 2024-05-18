<?php

namespace app\models;

use Yii;
use \app\models\base\SalesDeleted as BaseSalesDeleted;

/**
 * This is the model class for table "sales_deleted".
 */
class SalesDeleted extends BaseSalesDeleted
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['id', 'billId', 'at', 'clinet', 'payWay', 'branch', 'total', 'type', 'user_insert', 'created_at'], 'required'],
            [['id', 'billId', 'clinet', 'payWay', 'branch', 'type', 'carpenter', 'upholstered', 'paintId', 'deleviryId', 'user_insert', 'user_update'], 'integer'],
            [['at', 'deleviryAt', 'created_at', 'update_at'], 'safe'],
            [['total', 'paid'], 'number'],
            [['notes', 'path'], 'string', 'max' => 255]
        ]);
    }
	
}
