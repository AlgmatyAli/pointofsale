<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TempTransferItems]].
 *
 * @see TempTransferItems
 */
class TempTransferItemsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TempTransferItems[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TempTransferItems|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
