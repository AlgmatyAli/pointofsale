<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receipt_arch".
 *
 * @property int $id
 * @property int $rId
 * @property int $clinet
 * @property string $at
 * @property float $value
 * @property string $why
 * @property string $payWay
 * @property int $type
 * @property int|null $delete_by
 * @property int|null $delete_at
 */
class ReceiptArch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receipt_arch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rId', 'clinet', 'at', 'value', 'why', 'payWay', 'type'], 'required'],
            [['rId', 'clinet', 'type', 'delete_by', 'delete_at'], 'integer'],
            [['at'], 'safe'],
            [['value'], 'number'],
            [['payWay'], 'string'],
            [['why'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'rId' => Yii::t('app', 'R ID'),
            'clinet' => Yii::t('app', 'Clinet'),
            'at' => Yii::t('app', 'At'),
            'value' => Yii::t('app', 'Value'),
            'why' => Yii::t('app', 'Why'),
            'payWay' => Yii::t('app', 'Pay Way'),
            'type' => Yii::t('app', 'Type'),
            'delete_by' => Yii::t('app', 'Delete By'),
            'delete_at' => Yii::t('app', 'Delete At'),
        ];
    }

    public function getC()
    {
        return $this->hasOne(Client::className(), ['id' => 'clinet']);
    }
}
