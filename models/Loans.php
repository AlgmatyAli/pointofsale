<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loans".
 *
 * @property int $id
 * @property int $employee
 * @property float|null $loanValue
 * @property float|null $kestValue
 * @property string|null $at
 * @property int $parts
 * @property int|null $paid
 * @property string|null $notes
 * @property int $status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Employee $employee0
 */
class Loans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $salary, $drawing, $min_date, $max_date, $lastPay, $lastPayDate;
    public static function tableName()
    {
        return 'loans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee', 'parts', 'status'], 'required'],
            [['employee', 'parts', 'paid', 'status', 'created_by', 'updated_by'], 'integer'],
            [['loanValue', 'kestValue'], 'number'],
            [['at', 'created_at', 'updated_at'], 'safe'],
            [['notes'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['employee'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'employee' => Yii::t('app', 'Employee'),
            'loanValue' => Yii::t('app', 'Loan Value'),
            'kestValue' => Yii::t('app', 'Kest Value'),
            'at' => Yii::t('app', 'At'),
            'parts' => Yii::t('app', 'Parts'),
            'paid' => Yii::t('app', 'Paid'),
            'notes' => Yii::t('app', 'Notes'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'salary' => Yii::t('app', 'Salary'),
            'lastPay' => Yii::t('app', 'Last Pay'),
            'lastPayDate' => Yii::t('app', 'Last Pay Date'),
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[Employee0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee0()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee']);
    }
}
