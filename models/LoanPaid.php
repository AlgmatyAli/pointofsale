<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loanPaid".
 *
 * @property int $id
 * @property int $employee
 * @property int $loanId
 * @property float|null $kestValue
 * @property string|null $at
 * @property int|null $month
 * @property int|null $year
 * @property string|null $notes
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Employee $employee0
 * @property Loans $loan
 */
class LoanPaid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loanPaid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee', 'loanId'], 'required'],
            [['employee', 'loanId', 'month', 'year', 'created_by', 'updated_by'], 'integer'],
            [['kestValue'], 'number'],
            [['at', 'created_at', 'updated_at'], 'safe'],
            [['notes'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['employee'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee' => 'id']],
            [['loanId'], 'exist', 'skipOnError' => true, 'targetClass' => Loans::className(), 'targetAttribute' => ['loanId' => 'id']],
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
            'loanId' => Yii::t('app', 'Loan ID'),
            'kestValue' => Yii::t('app', 'Kest Value'),
            'at' => Yii::t('app', 'At'),
            'month' => Yii::t('app', 'Month'),
            'year' => Yii::t('app', 'Year'),
            'notes' => Yii::t('app', 'Notes'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
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

    /**
     * Gets query for [[Loan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loanId']);
    }
}
