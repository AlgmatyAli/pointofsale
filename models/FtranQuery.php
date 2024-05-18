<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Ftran]].
 *
 * @see Ftran
 */
class FtranQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Ftran[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Ftran|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
