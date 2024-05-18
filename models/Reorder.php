<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reorder".
 *
 * @property int $id
 * @property string|null $at
 * @property int $branch
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Branches $branch0
 * @property ReorderDetails[] $reorderDetails
 */
class Reorder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reorder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['at', 'created_at', 'updated_at'], 'safe'],
            [['branch'], 'required'],
            [['branch', 'created_by', 'updated_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['branch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'at' => Yii::t('app', 'At'),
            'branch' => Yii::t('app', 'Branch'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[Branch0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch0()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch']);
    }

    /**
     * Gets query for [[ReorderDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReorderDetails()
    {
        return $this->hasMany(ReorderDetails::className(), ['reorder' => 'id']);
    }
}
