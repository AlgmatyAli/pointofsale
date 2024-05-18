<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reorderDetails".
 *
 * @property int $id
 * @property int $reorder
 * @property int $category
 * @property float $quantity
 *
 * @property Category $category0
 * @property Reorder $reorder0
 */
class ReorderDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reorderDetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reorder', 'category', 'quantity'], 'required'],
            [['reorder', 'category'], 'integer'],
            [['quantity'], 'number'],
            [['category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category' => 'id']],
            [['reorder'], 'exist', 'skipOnError' => true, 'targetClass' => Reorder::className(), 'targetAttribute' => ['reorder' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'reorder' => Yii::t('app', 'Reorder'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
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
     * Gets query for [[Reorder0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReorder0()
    {
        return $this->hasOne(Reorder::className(), ['id' => 'reorder']);
    }
}
