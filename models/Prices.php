<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prices".
 *
 * @property int $id
 * @property int $category
 * @property float $costPrice
 * @property float|null $minPrice
 * @property float|null $minPrice2
 * @property float|null $minPrice3
 * @property float|null $maxPrice
 *
 * @property Category $category0
 */
class Prices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $serialNo, $quantity, $company, $lowPrice, $bigPrice, $percentage, $zeroQnty;
    public static function tableName()
    {
        return 'prices';
    }

    /**
     * {@inheritdoc}
     */
    
    public function rules()
    {
        return [
            [['category', 'costPrice'], 'required'],
            [['category'], 'integer'],
            [['serialNo', 'quantity', 'company', 'lowPrice', 'bigPrice', 'percentage', 'zeroQnty'], 'safe'],
            [['costPrice', 'minPrice', 'minPrice2', 'minPrice3', 'maxPrice'], 'number'],
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
            'category' => Yii::t('app', 'Category'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'minPrice' => Yii::t('app', 'Min Price'),
            'minPrice2' => Yii::t('app', 'Min Price2'),
            'minPrice3' => Yii::t('app', 'Min Price3'),
            'maxPrice' => Yii::t('app', 'Max Price'),
            'serialNo' => Yii::t('app', 'Serial No'),
            'lowPrice' => Yii::t('app', 'Low Price'),
            'bigPrice' => Yii::t('app', 'Big Price'),
            'Percentage' => Yii::t('app', 'Percentage of increase'),
            'zeroQnty' => Yii::t('app', 'Zero Qnty'),
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

    public function getStocks0()
    {
        return $this->hasOne(Stocks::class, ['category' => 'category']);
    }
}
