<?php

namespace app\models\base;

use Yii;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "tempTransferItems".
 *
 * @property integer $id
 * @property integer $category
 * @property string $quantity
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property \app\models\User $createdBy
 * @property \app\models\User $updatedBy
 * @property \app\models\Category $category0
 */
class TempTransferItems extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
     * This function helps \mootensai\relation\RelationTrait runs faster
     * @return array relation names of this model
     */
    public function relationNames()
    {
        return [
            'createdBy',
            'updatedBy',
            'category0'
        ];
    }

    /**
     * @inheritdoc
     */

    public $branch;
    public function rules()
    {
        return [
            [['id', 'category', 'quantity'], 'required'],
            [['id', 'category', 'created_by', 'updated_by'], 'integer'],
            [['quantity'], 'number'],
            [['created_at', 'updated_at', 'branch'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tempTransferItems';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'quantity' => Yii::t('app', 'Quantity'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory0()
    {
        return $this->hasOne(\app\models\Category::className(), ['id' => 'category']);
    }

    /**
     * @inheritdoc
     * @return \app\models\TempTransferItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\TempTransferItemsQuery(get_called_class());
    }
}
