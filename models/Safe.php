<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "safe".
 *
 * @property int $id
 * @property int $branch
 * @property float $value
 * @property string $at
 * @property int $type
 * @property string $why
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 *
 * @property Branches $br
 * @property User $userInsert
 * @property User $userUpdate
 */
class Safe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'safe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch', 'value', 'at', 'type', 'why', 'user_insert', 'created_at'], 'required'],
            [['branch', 'type', 'user_insert', 'user_update','safeNo'], 'integer'],
            [['value'], 'number'],
            [['at', 'created_at', 'update_at','safeNo'], 'safe'],
            [['why'], 'string', 'max' => 255],
            [['branch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch' => 'id']],
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
            'branch' => Yii::t('app', 'Br ID'),
            'value' => Yii::t('app', 'Value'),
            'at' => Yii::t('app', 'At'),
            'type' => Yii::t('app', 'Type'),
            'why' => Yii::t('app', 'Why'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'safeNo' => Yii::t('app', 'Safe No.'),
        ];
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
