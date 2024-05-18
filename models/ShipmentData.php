<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipmentData".
 *
 * @property int $id
 * @property int $shipmentId
 * @property string|null $at
 * @property int $size
 * @property int $country
 * @property int $type
 * @property string|null $notes
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class ShipmentData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipmentData';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shipmentId', 'size', 'country', 'type', 'value', 'customsOffice', 'currancy'], 'required'],
            [['shipmentId', 'created_by', 'updated_by', 'currancy'], 'integer'],
            [['value'], 'number'],
            [['at', 'created_at', 'updated_at'], 'safe'],
            [['notes', 'country', 'type', 'size'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shipmentId' => Yii::t('app', 'Shipment ID'),
            'at' => Yii::t('app', 'At'),
            'size' => Yii::t('app', 'Size'),
            'country' => Yii::t('app', 'Country'),
            'type' => Yii::t('app', 'Type Of Shipment'),
            'notes' => Yii::t('app', 'Notes'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'value' => Yii::t('app', 'Value'),
            'currancy' => Yii::t('app', 'Currancy'),
            'customsOffice' => Yii::t('app', 'Customs Office'),
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

    public function getCustom()
    {
        return $this->hasOne(CustomsDeclaration::className(), ['id' => 'customsOffice']);
    }

    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::className(), ['id' => 'currancy']);
    }
}
