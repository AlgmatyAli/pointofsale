<?php

namespace app\models;

use Yii;
use \app\models\base\TempBackSales as BaseTempBackSales;

/**
 * This is the model class for table "temp_back_sales".
 */
class TempBackSales extends BaseTempBackSales
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['category', 'quantity', 'salePrice'], 'required'],
            [['category', 'box', 'state', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
            [['expire', 'created_at', 'updated_at'], 'safe'],
            [['serial_number'], 'string', 'max' => 255],
        ]);
    }
	
}
