<?php

namespace app\models;

use Yii;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

/**
 * This is the model class for table "inventory".
 *
 * @property int $id
 * @property string $name
 * @property float $quantity
 * @property int $box
 * @property string $unit
 * @property string $class
 * @property int $branch
 */
class Inventory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $min_date;
    public $max_date;
    Public $costPrice,$maxPrice,$minPrice, $allData, $at, $client;
    public static function tableName()
    {
        return 'inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'box', 'branch'], 'integer'],
            [['quantity'], 'number'],
            [['name', 'class'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 100],
            [['tranDate', 'min_date', 'max_date', 'commCode', 'allData', 'at', 'maxPrice', 'client'], 'safe'],

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
            'client' => Yii::t('app', 'Client'),
        ];
    }

    public function getPrices0()
    {
        return $this->hasOne(Prices::class, ['category' => 'id']);
    }
    public function getBranches0()
    {
        return $this->hasOne(Branches::class, ['id' => 'branch']);
    } 
    public function getCategory0()
    {
        return $this->hasOne(Category::class, ['id' => 'id']);
    } 
}
