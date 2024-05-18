<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property int $claimId
 * @property string $path
 * @property string $name
 * @property int $user_insert
 * @property int $user_update
 * @property string $created_at
 * @property string $update_at
 *
 * @property User $claim
 * @property User $userInsert
 * @property User $userUpdate
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $file ;
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'path', 'name', 'user_insert', 'user_update', 'created_at', 'update_at'], 'required'],
            [['claimId', 'user_insert', 'user_update'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['path'], 'string', 'max' => 1024],
            [['name'], 'string', 'max' => 255],
            [['claimId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['claimId' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['file'], 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'claimId' => Yii::t('app', 'Claim ID'),
            'path' => Yii::t('app', 'Path'),
            'name' => Yii::t('app', 'Name'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'user_update' => Yii::t('app', 'User Update'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
            'file'=>Yii::t('app', 'img'),
        ];
    }

    /**
     * Gets query for [[Claim]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaim()
    {
        return $this->hasOne(User::className(), ['id' => 'claimId']);
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
