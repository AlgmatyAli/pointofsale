<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property int $id
 * @property int $rId
 * @property int $clinet
 * @property string $at
 * @property float $value
 * @property string $why
 * @property string $payWay
 * @property int $type
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property int $branch
 *
 * @property Client $c
 * @property User $userInsert
 * @property User $userUpdate
 */
class Receipt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rId', 'clinet', 'at', 'value', 'why', 'payWay', 'type', 'user_insert', 'created_at', 'branch'], 'required'],
            [['rId', 'clinet', 'type', 'user_insert','currancy', 'user_update', 'agent','branch'], 'integer'],
            [['at', 'created_at','currancy', 'update_at','agent' ,'tafqet'], 'safe'],
            [['value'], 'number'],
            [['payWay'], 'string'],
            [['why'], 'string', 'max' => 255],
            [['clinet'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['clinet' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'rId' => Yii::t('app', 'R ID'),
            'clinet' => Yii::t('app', 'C ID'),
            'at' => Yii::t('app', 'At'),
            'value' => Yii::t('app', 'Value'),
            'why' => Yii::t('app', 'Why'),
            'agent' => Yii::t('app', 'Agent'),
            'payWay' => Yii::t('app', 'Pay Way'),
            'type' => Yii::t('app', 'Type'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'branch' => Yii::t('app', 'Br ID'),
            'currancy' => Yii::t('app', 'Currancy'),
            'tafqet' => Yii::t('app', 'Tafqet'),
        ];
    }

    /**
     * Gets query for [[C]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getC()
    {
        return $this->hasOne(Client::className(), ['id' => 'clinet']);
    }

    /**
     * Gets query for [[UserInsert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserInsert()
    {
        return $this->hasOne(User::className(), ['id' => 'user_insert']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(User::className(), ['id' => 'user_update']);
    }

    public function getAgent0()
    {
        return $this->hasOne(Agent::className(), ['id' => 'agent']);
    }

     /**
     * Gets query for [[Br]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBr()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch']);
    }
    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::className(), ['id' => 'currancy']);
    }
}
