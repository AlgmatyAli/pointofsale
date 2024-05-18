<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hangouts_quantity".
 *
 * @property int $id
 * @property float $costPrice
 * @property float|null $totalCost
 * @property float|null $salePrice
 * @property float|null $salePrice_
 * @property int $PurchasesId
 * @property string|null $name
 * @property float|null $quantity
 * @property int|null $branch
 * @property int|null $box
 * @property string|null $class
 * @property string|null $company
 * @property string|null $unit
 * @property string|null $serialNo
 */
class HangoutsQuantity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hangouts_quantity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'costPrice', 'PurchasesId'], 'required'],
            [['id', 'PurchasesId', 'branch', 'box'], 'integer'],
            [['costPrice', 'totalCost', 'salePrice', 'salePrice_', 'quantity'], 'number'],
            [['name', 'class', 'company', 'serialNo'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 100],
        ];
    }

    public static function primaryKey()
      {
          return ["id"];
      }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'costPrice' => Yii::t('app', 'Cost Price'),
            'totalCost' => Yii::t('app', 'Total Cost'),
            'salePrice' => Yii::t('app', 'Sale Price'),
            'salePrice_' => Yii::t('app', 'Sale Price'),
            'PurchasesId' => Yii::t('app', 'Purchases ID'),
            'name' => Yii::t('app', 'Name'),
            'quantity' => Yii::t('app', 'Quantity'),
            'branch' => Yii::t('app', 'Branch'),
            'box' => Yii::t('app', 'Box'),
            'class' => Yii::t('app', 'Class'),
            'company' => Yii::t('app', 'Company'),
            'unit' => Yii::t('app', 'Unit'),
            'serialNo' => Yii::t('app', 'Serial No'),
        ];
    }
}
