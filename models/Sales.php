<?php

namespace app\models;

use app\models\base\Currancy;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;
use Yii;

/**
 * This is the model class for table "sales".
 *
 * @property int $id
 * @property int $billId
 * @property string $at
 * @property int $clinet
 * @property int $payWay
 * @property int $branch
 * @property float $total
 * @property float|null $paid
 * @property string|null $notes
 * @property string|null $path
 * @property int $type
 * @property string $deleviryAt
 * @property int $carpenter
 * @property int $upholstered
 * @property int $paintId
 * @property int $deleviryId
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property int|null $disscount
 * @property int|null $deleviried
 * @property int|null $wholesale
 * @property int|null $currancy
 * @property int|null $packing $name

 * @property Branches $br
 * @property User $carpenter0
 * @property Client $c
 * @property User $deleviry
 * @property User $paint
 * @property User $upholstered0
 * @property User $userInsert
 * @property User $userUpdate
 * @property SalesDetails[] $salesDetails
 */
class Sales extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $file;
    public $phone,$net,$category, $min_date, $max_date;
    public static function tableName()
    {
        return 'sales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['billId', 'at', 'clinet', 'payWay', 'branch', 'total', 'type', 'created_at', 'paid', 'disscount', 'currancy'], 'required'],
            [['billId', 'clinet', 'payWay','category', 'branch', 'type','net', 'carpenter', 'upholstered', 'paintId', 'deleviryId', 'user_insert', 'agent','user_update'], 'integer'],
            [['at', 'deleviryAt', 'created_at', 'update_at', 'category','phone', 'carpenter', 'upholstered', 'paintId', 'deleviryId', 'min_date', 'max_date','agent', 'deleviried', 'deserving', 'wholesale'], 'safe'],
            [['total', 'paid', 'disscount', 'user_insert', 'currancy'], 'number'],
            [['notes', 'path'], 'string', 'max' => 255],
            [['branch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch' => 'id']],
            [['carpenter'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['carpenter' => 'id']],
            [['clinet'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['clinet' => 'id']],
            [['deleviryId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleviryId' => 'id']],
            [['paintId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['paintId' => 'id']],
            [['upholstered'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['upholstered' => 'id']],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['currancy'], 'exist', 'skipOnError' => true, 'targetClass' => Currancy::className(), 'targetAttribute' => ['currancy' => 'id']],
            [['file'], 'file']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'billId' => Yii::t('app', 'Bill ID'),
            'at' => Yii::t('app', 'At'),
            'clinet' => Yii::t('app', 'C ID'),
            'payWay' => Yii::t('app', 'Pay Way'),
            'branch' => Yii::t('app', 'Br ID'),
            'net' => Yii::t('app', 'Net'),
            'total' => Yii::t('app', 'Total'),
            'paid' => Yii::t('app', 'Paid'),
            'notes' => Yii::t('app', 'Notes'),
            'path' => Yii::t('app', 'Path'),
            'type' => Yii::t('app', 'Type'),
            'agent' => Yii::t('app', 'Agent'),
            'deleviryAt' => Yii::t('app', 'Deleviry At'),
            'carpenter' => Yii::t('app', 'Carpenter'),
            'upholstered' => Yii::t('app', 'Upholstered'),
            'paintId' => Yii::t('app', 'Paint ID'),
            'deleviryId' => Yii::t('app', 'Deleviry ID'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'file'=>Yii::t('app', 'img'),
            'phone'=>Yii::t('app', 'Phone'),
            'min_date' => Yii::t('app', 'Min Date'),
            'max_date' => Yii::t('app', 'Max Date'),
            'disscount' => Yii::t('app', 'Disscount'),
            'deleviried'=> Yii::t('app', 'Deleviried'),
            'deserving' => Yii::t('app', 'Deserving'), 
            'wholesale' => Yii::t('app', 'Wholesale'),
            'currancy' => Yii::t('app', 'Currancy'),
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
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch']);
    }

    /**
     * Gets query for [[Carpenter0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarpenter0()
    {
        return $this->hasOne(User::className(), ['id' => 'carpenter']);
    }

    /**
     * Gets query for [[C]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getC()
    {
        return $this->hasOne(Client::className(), ['id' => 'clinet']);
    }

    /**
     * Gets query for [[Deleviry]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeleviry()
    {
        return $this->hasOne(User::className(), ['id' => 'deleviryId']);
    }

    /**
     * Gets query for [[Paint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaint()
    {
        return $this->hasOne(User::className(), ['id' => 'paintId']);
    }


    public function getAgent0()
    {
        return $this->hasOne(Agent::className(), ['id' => 'agent']);
    }

    /**
     * Gets query for [[Upholstered0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpholstered0()
    {
        return $this->hasOne(User::className(), ['id' => 'upholstered']);
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
     * Gets query for [[SalesDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::className(), ['salesId' => 'id']);
    }
}
