<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "totalinventory".
 *
 * @property string|null $company
 * @property int $id
 * @property int|null $type
 * @property string|null $name
 * @property float|null $quantity
 * @property string|null $unit
 * @property string|null $serialNo
 * @property int|null $box
 * @property string|null $class
 * @property int|null $branch
 * @property float|null $maxPrice
 */ 
class Totalinventory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Totalinventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'box', 'branch'], 'integer'],
            [['quantity', 'maxPrice'], 'number'],
            [['company', 'name', 'serialNo', 'class', 'commCode'], 'string', 'max' => 255],
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
            'company' => Yii::t('app', 'Company'),
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'quantity' => Yii::t('app', 'Quantity'),
            'unit' => Yii::t('app', 'Unit'),
            'serialNo' => Yii::t('app', 'Serial No'),
            'box' => Yii::t('app', 'Box'),
            'class' => Yii::t('app', 'Class'),
            'branch' => Yii::t('app', 'Branch'),
            'maxPrice' => Yii::t('app', 'Max Price'),
            'minPrice' => Yii::t('app', 'Min Price'),
            'place' => Yii::t('app', 'Place'),
            'commCode' => Yii::t('app', 'commCode'),
        ];
    }
}
