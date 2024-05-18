<?php

namespace app\models;

use Yii;
use \app\models\base\TempInvoicePurchase as BaseTempInvoicePurchase;

/**
 * This is the model class for table "temp_invoice_purchase".
 */
class TempInvoicePurchase extends BaseTempInvoicePurchase
{
    /**
     * @inheritdoc
     */
    public $rate, $totalInvoice, $totalCost; 
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['category', 'quantity', 'salePrice', 'costPrice', 'salePrice_'], 'required'],
            [['category', 'box', 'created_by', 'updated_by'], 'integer'],
            [['id', 'costTotal', 'quantity', 'costPrice', 'state', 'salePrice', 'salePrice_', 'rate', 'totalInvoice', 
            'totalCost', 'profit', 'salePrice_2', 'salePrice_3', 'derhamRate'], 'number'],
            [['expire', 'created_at', 'updated_at'], 'safe']
        ]);
    }
	
}
