<?php

namespace app\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "ftran".
 *
 * @property string $date_
 * @property string $description
 * @property string $wared
 * @property integer $sader
 * @property integer $payWay
 * @property integer $branch
 * @property integer $user_insert
 * @property integer $currancy
 */
class Ftran extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_', 'wared', 'payWay', 'branch'], 'required'],
            [['date_', 'today'], 'safe'],
            [['wared'], 'number'],
            [['sader', 'payWay', 'branch', 'user_insert'], 'integer'],
            [['description'], 'string', 'max' => 288]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ftran';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date_' => Yii::t('app', 'Date'),
            'description' => Yii::t('app', 'Description'),
            'wared' => Yii::t('app', 'Wared'),
            'sader' => Yii::t('app', 'Sader'),
            'payWay' => Yii::t('app', 'Pay Way'),
            'branch' => Yii::t('app', 'Branch'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'min_date' => Yii::t('app', 'Min Date'),
            'max_date' => Yii::t('app', 'Max Date'),
            'currancy' => Yii::t('app', 'Currancy'),
            'today' => Yii::t('app', 'Today'),
        ];
    }

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    public function getBranch0()
    {
        return $this->hasOne(\app\models\Branches::className(), ['id' => 'branch']);
    }
    public function getUser()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'user_insert']);
    }
    /**
     * @inheritdoc
     * @return \app\models\FtranQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\FtranQuery(get_called_class());
    }
}
