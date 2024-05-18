<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TempBackSales]].
 *
 * @see TempBackSales
 */
class TempBackSalesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TempBackSales[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TempBackSales|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
