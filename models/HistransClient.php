<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "histrans_client".
 *
 * @property int $sort 
 * @property string $kind
 * @property int $id
 * @property string $name
 
 * @property int $type
 * @property int $branch
 * @property string $trandate
 * @property int $BillId 
 * @property int $printId 
 * @property int|null $deleviried 
 * @property int|null $currency 
 */

class HistransClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $min_date, $max_date,  $allData;
    public $sumwared, $sumsader, $count;
    public static function tableName()
    {
        return 'histrans_client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'branch'], 'integer'],
            [['dept', 'credt', 'phone', 'deleviried', 'currency'], 'default', 'value' => null],
            [['printId'], 'default', 'value' => 0],
            [['trandate'], 'default', 'value' => ''],
            [['sort', 'id', 'type', 'branch', 'BillId', 'printId', 'deleviried', 'currency'], 'integer'],
            [['credt'], 'number'],
            [['trandate', 'min_date', 'max_date', 'billId', 'allData'], 'safe'],
            [['kind'], 'string', 'max' => 14],
            [['kind'], 'string', 'max' => 28],
            [['name', 'dept', 'phone'], 'string', 'max' => 255],
            [['trandate'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kind' => Yii::t('app', 'Kind'),
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'dept' => Yii::t('app', 'Dept'),
            'credt' => Yii::t('app', 'Credt'),
            'phone' => Yii::t('app', 'Phone'),
            'type' => Yii::t('app', 'Type'),
            'branch' => Yii::t('app', 'Br ID'),
            'trandate' => Yii::t('app', 'Trandate'),
            'min_date' => Yii::t('app', 'Min Date'),
            'max_date' => Yii::t('app', 'Max Date'),
            'allData' => Yii::t('app', 'All Data'),
            'BillId' => Yii::t('app', 'Bill ID'),
            'printId' => Yii::t('app', 'Print ID'),
            'deleviried' => Yii::t('app', 'Deleviried'),
            'currency' => Yii::t('app', 'Currency'),
        ];
    }
}
