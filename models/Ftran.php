<?php

namespace app\models;

use Yii;
use \app\models\base\Ftran as BaseFtran;

/**
 * This is the model class for table "ftran".
 */
class Ftran extends BaseFtran
{
    /**
     * @inheritdoc
     */
    public $min_date;
    public $max_date;
    public $today;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['date_', 'wared', 'payWay', 'branch', 'currancy'], 'required'],
            [['date_', 'today', 'min_date', 'max_date', ], 'safe'],
            [['wared'], 'number'],
            [['sader', 'payWay', 'branch', 'user_insert', 'currancy'], 'integer'],
            [['description'], 'string', 'max' => 288]
        ]);
    }
	
}
