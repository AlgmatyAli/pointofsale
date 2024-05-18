<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transferItems".
 *
 * @property int $id
 * @property int $fromBranch
 * @property int $toBranch
 * @property string $at
 * @property int|null $created_by
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 *
 * @property User $userInsert
 * @property User $userUpdate
 * @property Branches $fromBranch0
 * @property Branches $toBranch0
 * @property TransferItemsDetails[] $transferItemsDetails
 */
class TransferItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $category;
    public static function tableName()
    {
        return 'transferItems';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromBranch', 'toBranch', 'at', 'user_insert', 'created_at'], 'required'],
            [['fromBranch', 'toBranch', 'user_insert', 'user_update'], 'integer'],
            [['at', 'created_at', 'update_at', 'category'], 'safe'],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['fromBranch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['fromBranch' => 'id']],
            [['toBranch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['toBranch' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fromBranch' => Yii::t('app', 'From Branch'),
            'toBranch' => Yii::t('app', 'To Branch'),
            'at' => Yii::t('app', 'At'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'category'=> Yii::t('app', 'Category'),
        ];
    }

    /**
     * Gets query for [[UserInsert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserInsert()
    {
        return $this->hasOne(User::class, ['id' => 'user_insert']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'user_update']);
    }

    /**
     * Gets query for [[FromBranch0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromBranch0()
    {
        return $this->hasOne(Branches::class, ['id' => 'fromBranch']);
    }

    /**
     * Gets query for [[ToBranch0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToBranch0()
    {
        return $this->hasOne(Branches::className(), ['id' => 'toBranch']);
    }

    /**
     * Gets query for [[TransferItemsDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransferItemsDetails()
    {
        return $this->hasMany(TransferItemsDetails::className(), ['transfer' => 'id']);
    }
}
