<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "histrans_client".
 *
 * @property string $kind
 * @property int $id
 * @property string $name
 * @property string|null $dept
 * @property float|null $credt
 * @property string|null $phone
 * @property int $type
 * @property int $branch
 * @property string $trandate
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
            [['credt'], 'number'],
            [['trandate', 'min_date', 'max_date', 'billId', 'allData'], 'safe'],
            [['kind'], 'string', 'max' => 14],
            [['name', 'dept', 'phone'], 'string', 'max' => 255],
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
        ];
    }
}
