<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dept".
 *
 * @property float|null $credt
 * @property string|null $phone
 * @property int|null $type
 * @property string|null $deserving 
 * @property int|null $currency 
 */
class Dept extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dept';
    }
    public $indebtedness;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['name', 'credt', 'phone', 'type', 'deserving', 'currency', 'indebtedness'], 'default', 'value' => null],
            [['id'], 'default', 'value' => 0],
            [['id', 'type', 'currency'], 'integer'],
            [['credt'], 'number'],
            [['deserving', 'indebtedness'], 'safe'],
            [['name', 'phone'], 'string', 'max' => 255],
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
            'credt' => Yii::t('app', 'Credt'),
            'phone' => Yii::t('app', 'Phone'),
            'type' => Yii::t('app', 'Type'),
            'deserving' => Yii::t('app', 'Deserving'),
            'currency' => Yii::t('app', 'Currency'),
            'indebtedness' => Yii::t('app', 'Indebtedness'),
        ];
    }
}
