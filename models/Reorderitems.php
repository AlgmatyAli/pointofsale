<?php

namespace app\models;

use Yii;
use \app\models\base\Reorderitems as BaseReorderitems;

/**
 * This is the model class for table "reorderitems".
 */
class Reorderitems extends BaseReorderitems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['id', 'minimum'], 'integer'],
            [['quantity'], 'number'],
            [['name', 'serialNo', 'class', 'company'], 'string', 'max' => 255],
            [['lock'], 'default', 'value' => '0'],
            [['lock'], 'mootensai\components\OptimisticLockValidator']
        ]);
    }
	
}
