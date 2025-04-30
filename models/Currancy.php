<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currancy".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int|null $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property CompanyInfo[] $companyInfos
 * @property EmpSalary[] $empSalaries
 * @property Expenses[] $expenses
 * @property Purchases[] $purchases
 * @property Receipt[] $receipts
 * @property Sales[] $sales
 * @property Shipmentdata[] $shipmentdatas
 * @property User $updatedBy
 * @property User $userInsert
 */
class Currancy extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currancy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_insert', 'user_update', 'update_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['name', 'code', 'created_at'], 'required'],
            [['user_insert', 'user_update', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 3],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_insert' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
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
            'code' => Yii::t('app', 'Code'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[CompanyInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyInfos()
    {
        return $this->hasMany(CompanyInfo::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[EmpSalaries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmpSalaries()
    {
        return $this->hasMany(EmpSalary::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[Expenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(Expenses::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[Purchases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchases()
    {
        return $this->hasMany(Purchases::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[Receipts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceipts()
    {
        return $this->hasMany(Receipt::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[Sales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sales::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[Shipmentdatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShipmentdatas()
    {
        return $this->hasMany(Shipmentdata::class, ['currancy' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
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

}
