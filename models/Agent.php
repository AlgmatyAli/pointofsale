<?php

namespace app\models;

use Yii;
use \app\models\base\Agent as BaseAgent;

/**
 * This is the model class for table "agent".
 */
class Agent extends BaseAgent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'phone', 'email'], 'required'],
            [['branch', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'branch'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 20]
        ]);
    }
	
}
