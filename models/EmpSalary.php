<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "emp_salary".
 *
 * @property int $id
 * @property int $employee
 * @property string $at
 * @property float $value
 * @property string $why
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $user
 * @property User $userInsert
 * @property User $userUpdate
 */
class EmpSalary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $salary, $drawing, $min_date, $max_date, $lastPay, $lastPayDate, $remaining,$totalPay;
    public static function tableName()
    {
        return 'emp_salary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee', 'at', 'value', 'why', 'created_by', 'created_at', 'currancy'], 'required'],
            [['employee', 'created_by', 'updated_by', 'type', 'currancy'], 'integer'],
            [['at', 'created_at', 'updated_at', 'month', 'year', 'min_date', 'max_date', 'lastPay', 'lastPayDate', 'totalPay'], 'safe'],
            [['value'], 'number'],
            [['why'], 'string', 'max' => 255],
            [['employee'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'employee' => Yii::t('app', 'Employee'),
            'at' => Yii::t('app', 'At'),
            'value' => Yii::t('app', 'Value'),
            'why' => Yii::t('app', 'Why'),
            'created_by' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'User Update'),
            'updated_at' => Yii::t('app', 'Update At'),
            'month' => Yii::t('app', 'Month'),
            'year' => Yii::t('app', 'Year'),
            'drawing' => Yii::t('app', 'Drawing'),
            'salary' => Yii::t('app', 'Salary'),
            'lastPay' => Yii::t('app', 'Last Pay'),
            'lastPayDate' => Yii::t('app', 'Last Pay Date'),
            'remaining' => Yii::t('app', 'Remaining'),
            'currancy' => Yii::t('app', 'Currancy'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmp()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee']);
    }

    /**
     * Gets query for [[UserInsert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserInsert()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
