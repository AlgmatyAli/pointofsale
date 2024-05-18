<?php

namespace app\models;

use Yii;
use \app\models\base\TempInvoice as BaseTempInvoice;

/**
 * This is the model class for table "temp_invoice".
 */
class TempInvoice extends BaseTempInvoice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['invoice_number','type', 'category', 'box', 'state', 'created_by', 'updated_by', 'cat', 'kind'], 'integer'],
            //[['category'], 'required'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
            [['expire', 'mac_address','type','created_at', 'updated_at', 'waitQnty'], 'safe'],
            [['serial_number'], 'string', 'max' => 255]
        ]);
    }
	
}
