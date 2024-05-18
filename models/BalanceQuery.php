<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Balance]].
 *
 * @see Balance
 */
class BalanceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Balance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Balance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
