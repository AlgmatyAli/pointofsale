<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transferItemsDetails".
 *
 * @property int $id
 * @property int $transfer
 * @property int $category
 * @property int $quantity
 *
 * @property TransferItems $transfer0
 * @property Category $category0
 */
class TransferItemsDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transferItemsDetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transfer', 'category', 'quantity'], 'required'],
            [['transfer', 'category', 'quantity'], 'integer'],
            [['transfer'], 'exist', 'skipOnError' => true, 'targetClass' => TransferItems::className(), 'targetAttribute' => ['transfer' => 'id']],
            [['category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'transfer' => Yii::t('app', 'Transfer'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
        ];
    }

    /**
     * Gets query for [[Transfer0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransfer0()
    {
        return $this->hasOne(TransferItems::className(), ['id' => 'transfer']);
    }

    /**
     * Gets query for [[Category0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory0()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }
}
