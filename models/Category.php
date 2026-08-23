<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string $class
 * @property string $unit
 * @property int $box
 * @property float $cost
 * @property float $price
 * @property float $quantity
 * @property int $minimum
 * @property int $ending
 * @property int $qShow
 * @property int $status
 * @property string|null $serialNo
 * @property string|null $country
 * @property string|null $company
 * @property string|null $path
 * @property int $user_insert
 * @property string $created_at
 * @property int|null $user_update
 * @property string|null $update_at
 * @property string $place
 * @property string $commCode
 * @property string $moreRequest
 * @property string $weight
 * @property User $userInsert
 * @property User $userUpdate
 * @property Prices[] $prices
 * @property PurchasesDetails[] $purchasesDetails
 * @property SalesDetails[] $salesDetails
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $file;
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'class', 'unit', 'box', 'minimum', 'ending', 'qShow', 'status', 'user_insert', 'created_at'], 'required'],
            [['box', 'minimum', 'ending', 'qShow', 'status', 'user_insert', 'user_update'], 'integer'],
            [['cost', 'price', 'quantity'], 'number'],
            [['created_at', 'update_at', 'place', 'commCode', 'moreRequest', 'cost', 'price', 'quantity', 'weight'], 'safe'],
            [['name', 'class', 'serialNo', 'country', 'company', 'path'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 100],
            [['user_insert'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_insert' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['file'], 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 1024 * 1024 * 2],
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
            'class' => Yii::t('app', 'Class'),
            'unit' => Yii::t('app', 'Unit'),
            'box' => Yii::t('app', 'Box'),
            'cost' => Yii::t('app', 'Cost'),
            'price' => Yii::t('app', 'Price'),
            'quantity' => Yii::t('app', 'quantity'),
            'minimum' => Yii::t('app', 'Minimum'),
            'ending' => Yii::t('app', 'Ending'),
            'qShow' => Yii::t('app', 'Q Show'),
            'status' => Yii::t('app', 'Status'),
            'serialNo' => Yii::t('app', 'Serial No'),
            'country' => Yii::t('app', 'Country'),
            'company' => Yii::t('app', 'Company'),
            'path' => Yii::t('app', 'Path'),
            'user_insert' => Yii::t('app', 'User Insert'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_update' => Yii::t('app', 'User Update'),
            'update_at' => Yii::t('app', 'Update At'),
            'place' => Yii::t('app', 'Place'),
            'commCode' => Yii::t('app', 'Comm Code'),
            'moreRequest' => Yii::t('app', 'More Request'),
            'weight' => Yii::t('app', 'Weight'),
        ];
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
     * Gets query for [[Prices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(Prices::className(), ['category' => 'id']);
    }

    /**
     * Gets query for [[PurchasesDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchasesDetails()
    {
        return $this->hasMany(PurchasesDetails::className(), ['category' => 'id']);
    }

    /**
     * Gets query for [[SalesDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::className(), ['category' => 'id']);
    }

    public function getTotal()
    {
        return $this->hasOne(Totalinventory::className(), ['id' => 'id']);
    }
}
