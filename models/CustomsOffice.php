<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customsOffice".
 *
 * @property int $id
 * @property int $customId
 * @property string $at
 * @property float $value
 * @property string $why
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property string|null $currancy
 *
 * @property CustomsDeclaration $custom
 * @property User $userInsert
 * @property User $userUpdate
 */
class CustomsOffice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customsOffice';
    }

    /**
     * {@inheritdoc}
     */
    public $min_date, $max_date, $allData;
    public function rules()
    {
        return [
            [['customId', 'at', 'value', 'why', 'user_insert', 'created_at'], 'required'],
            [['customId', 'user_insert', 'user_update'], 'integer'],
            [['at', 'created_at', 'update_at', 'min_date', 'max_date', 'allData', 'currancy'], 'safe'],
            [['value'], 'number'],
            [['why'], 'string', 'max' => 255],
            [['customId'], 'exist', 'skipOnError' => true, 'targetClass' => CustomsDeclaration::className(), 'targetAttribute' => ['customId' => 'id']],
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
            'customId' => Yii::t('app', 'Custom ID'),
            'at' => Yii::t('app', 'At'),
            'value' => Yii::t('app', 'Value'),
            'why' => Yii::t('app', 'Why'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'min_date' => Yii::t('app', 'Min Date'), 
            'max_date' => Yii::t('app', 'Max Date'), 
            'allData' => Yii::t('app', 'All Date'),
            'currancy' => Yii::t('app', 'Currancy'),
            'customId' => Yii::t('app', 'Custom ID'),
        ];
    }

    /**
     * Gets query for [[Custom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustom()
    {
        return $this->hasOne(CustomsDeclaration::className(), ['id' => 'customId']);
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

    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::className(), ['id' => 'currancy']);
    }
}
