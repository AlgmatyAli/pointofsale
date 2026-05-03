<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "expenses".
 *
 * @property int $id
 * @property string $expenseTo
 * @property string $at
 * @property int $itemId
 * @property float $value
 * @property string $why
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property int $branch
 *
 * @property Items $item
 * @property User $userInsert
 * @property User $userUpdate
 */
class Expenses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expenses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expenseTo', 'at', 'itemId', 'value', 'why', 'user_insert', 'created_at', 'branch', 'outBox', 'currancy', 'payment_type'], 'required'],
            [['at', 'created_at', 'update_at'], 'safe'],
            [['itemId', 'user_insert', 'user_update', 'branch', 'currancy', 'payment_type'], 'integer'],
            [['value'], 'number'],
            [['expenseTo', 'why'], 'string', 'max' => 255],
            [['itemId'], 'exist', 'skipOnError' => true, 'targetClass' => Items::class, 'targetAttribute' => ['itemId' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_update' => 'id']],
            [['currancy'], 'exist', 'skipOnError' => true, 'targetClass' => Currancy::class, 'targetAttribute' => ['currancy' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'expenseTo' => Yii::t('app', 'Expense To'),
            'at' => Yii::t('app', 'At'),
            'itemId' => Yii::t('app', 'Item ID'),
            'value' => Yii::t('app', 'Value'),
            'why' => Yii::t('app', 'Why'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'branch' => Yii::t('app', 'Br ID'),
            'outBox' => Yii::t('app', 'Out Box'),
            'currancy' => Yii::t('app', 'Currancy'),
            'payment_type' => Yii::t('app', 'Payment Type'),

        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::class, ['id' => 'itemId']);
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
     * Gets query for [[Br]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBr()
    {
        return $this->hasOne(Branches::class, ['id' => 'branch']);
    }

    public function getPaymentType0()
    {
        return $this->hasOne(PaymentTypes::class, ['id' => 'payment_type']);
    }
}
