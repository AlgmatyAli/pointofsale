<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BalanceHistory]].
 *
 * @see BalanceHistory
 */
class BalanceHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return BalanceHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return BalanceHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
