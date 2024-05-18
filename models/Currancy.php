<?php

namespace app\models;

use Yii;
use \app\models\base\Currancy as BaseCurrancy;

/**
 * This is the model class for table "currancy".
 */
class Currancy extends BaseCurrancy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'code', 'created_at'], 'required'],
            [['user_insert', 'user_update', 'purchases', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 3]
        ]);
    }
	
}
