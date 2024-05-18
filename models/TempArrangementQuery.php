<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TempArrangement]].
 *
 * @see TempArrangement
 */
class TempArrangementQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TempArrangement[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TempArrangement|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
