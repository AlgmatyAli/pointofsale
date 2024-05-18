<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customsDeclaration".
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $balance
 * @property int $user_insert
 * @property int|null $user_update
 * @property string $created_at
 * @property string|null $update_at
 *
 * @property User $userInsert
 * @property User $userUpdate
 * @property CustomsOffice[] $customsOffices
 */
class CustomsDeclaration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customsDeclaration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'user_insert', 'created_at'], 'required'],
            [['user_insert', 'user_update'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['name', 'phone', 'balance'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'balance' => Yii::t('app', 'Balance'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'user_update' => Yii::t('app', 'User Update'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
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

    /**
     * Gets query for [[CustomsOffices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomsOffices()
    {
        return $this->hasMany(CustomsOffice::className(), ['customId' => 'id']);
    }
}
