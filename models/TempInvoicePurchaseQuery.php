<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TempInvoicePurchase]].
 *
 * @see TempInvoicePurchase
 */
class TempInvoicePurchaseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TempInvoicePurchase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TempInvoicePurchase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
