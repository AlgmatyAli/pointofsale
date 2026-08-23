<?php

namespace app\models;

use Yii;
use \app\models\base\TempTransferItems as BaseTempTransferItems;

/**
 * This is the model class for table "tempTransferItems".
 */
class TempTransferItems extends BaseTempTransferItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(
            parent::rules(),
            [
                [['id', 'category', 'quantity'], 'required'],
                [['id', 'category', 'created_by', 'updated_by'], 'integer'],
                [['quantity'], 'number'],
                [['created_at', 'updated_at', 'branch'], 'safe'],
            ]
        );
    }
}
