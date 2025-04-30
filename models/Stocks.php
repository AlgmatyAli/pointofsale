<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stocks".
 *
 * @property int $id
 * @property int $category
 * @property float $quantity
 *
 * @property Category $category0
 */
class Stocks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'stocks';
    }

    /**
     * {@inheritdoc}
     */
    public $serialNo, $class, $company, $commCode, $name,  $costPrice, $branchName, $status;
    public $maxPrice, $minPrice, $minPrice2, $minPrice3, $tq;
    public function rules()
    {
        return [
            [['category', 'quantity'], 'required'],
            [['category'], 'integer'],
            [['quantity'], 'number'],
            [['serialNo', 'class', 'company', 'commCode', 'name', 'maxPrice', 'minPrice', 'costPrice', 'minPrice2', 'minPrice3', 'tq', 'branchName', 'status'], 'safe'],
            [['category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category' => 'id']],
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
            'quantity' => Yii::t('app', 'quantity'),
            'box' => Yii::t('app', 'Box'),
            'unit' => Yii::t('app', 'Unit'),
            'class' => Yii::t('app', 'Class'),
            'branch' => Yii::t('app', 'Br ID'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'serianNo' => Yii::t('app', 'Serial No'),
            'commCode' => Yii::t('app', 'Comm Code'),
            'allData' => Yii::t('app', 'All Data'),
            'company' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * Gets query for [[Category0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory0()
    {
        return $this->hasOne(Category::class, ['id' => 'category']);
    }

    public function getPrices()
    {
        return $this->hasOne(Prices::class, ['category' => 'category']);
    }

    public function getBranches0()
    {
        return $this->hasOne(Branches::class, ['id' => 'branch']);
    }
}
