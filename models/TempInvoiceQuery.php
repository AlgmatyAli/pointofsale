<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TempInvoice]].
 *
 * @see TempInvoice
 */
class TempInvoiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TempInvoice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TempInvoice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
