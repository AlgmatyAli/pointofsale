<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "arrangementDetails".
 *
 * @property int $id
 * @property int $arrangement
 * @property int $category
 * @property float $quantity
 * @property int|null $box
 * @property int $type
 * @property string|null $expire
 * @property int|null $stockTaking
 * @property int|null $branch
 *
 * @property Category $category0
 * @property Arrangement $arrangement0
 */
class ArrangementDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arrangementDetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['arrangement', 'category', 'quantity', 'type'], 'required'],
            [['arrangement', 'category', 'box', 'type', 'stockTaking'], 'integer'],
            [['quantity'], 'number'],
            [['expire'], 'safe'],
            [['category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category' => 'id']],
            [['arrangement'], 'exist', 'skipOnError' => true, 'targetClass' => Arrangement::className(), 'targetAttribute' => ['arrangement' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'arrangement' => Yii::t('app', 'Arrangement'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
            'box' => Yii::t('app', 'Box'),
            'type' => Yii::t('app', 'Type'),
            'expire' => Yii::t('app', 'Expire'),
            'stockTaking' => Yii::t('app', 'Stock Taking'), 
        ];
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

    /**
     * Gets query for [[Arrangement0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArrangement0()
    {
        return $this->hasOne(Arrangement::className(), ['id' => 'arrangement']);
    }
}
