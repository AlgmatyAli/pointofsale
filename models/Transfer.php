<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transfer".
 *
 * @property int $id
 * @property int $fromBr
 * @property int $toBr
 * @property float $value
 * @property string $at
 * @property int $type
 * @property string $why
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 *
 * @property Branches $fromBr0
 * @property Branches $toBr0
 * @property User $userInsert
 * @property User $userUpdate
 */
class Transfer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromBr', 'toBr', 'value', 'at', 'type', 'why', 'user_insert', 'created_at', 'currancy'], 'required'],
            [['fromBr', 'toBr', 'type', 'user_insert', 'user_update', 'currancy'], 'integer'],
            [['value'], 'number'],
            [['at', 'created_at', 'update_at'], 'safe'],
            [['why'], 'string', 'max' => 255],
            [['fromBr'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['fromBr' => 'id']],
            [['toBr'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['toBr' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['currancy'], 'exist', 'skipOnError' => true, 'targetClass' => Currancy::className(), 'targetAttribute' => ['currancy' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fromBr' => Yii::t('app', 'From Br'),
            'toBr' => Yii::t('app', 'To Br'),
            'value' => Yii::t('app', 'Value'),
            'at' => Yii::t('app', 'At'),
            'type' => Yii::t('app', 'Type'),
            'why' => Yii::t('app', 'Why'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'currancy' => Yii::t('app', 'Currancy'),
        ];
    }

    /**
     * Gets query for [[FromBr0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromBr0()
    {
        return $this->hasOne(Branches::className(), ['id' => 'fromBr']);
    }

    /**
     * Gets query for [[ToBr0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToBr0()
    {
        return $this->hasOne(Branches::className(), ['id' => 'toBr']);
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
}
