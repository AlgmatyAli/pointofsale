<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "credts".
 *
 * @property string $kind
 * @property int $id
 * @property string $name
 * @property float|null $balance
 * @property string|null $phone
 * @property int $type
 */
class Credts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['balance'], 'number'],
            [['kind'], 'string', 'max' => 14],
            [['name', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kind' => Yii::t('app', 'Kind'),
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'balance' => Yii::t('app', 'Balance'),
            'phone' => Yii::t('app', 'Phone'),
            'type' => Yii::t('app', 'Type'),
        ];
    }
}
