<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dept_supp".
 *
 * @property int $id
 * @property string|null $name
 * @property float|null $credt
 * @property string|null $phone
 * @property int|null $type
 * 	@property int|null $currency 
 */
class DeptSupp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dept_supp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['name', 'credt', 'phone', 'type', 'currency'], 'default', 'value' => null],
            [['id'], 'default', 'value' => 0],
            [['id', 'type', 'currency'], 'integer'],
            [['credt'], 'number'],
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
            'currency' => Yii::t('app', 'Currency'),
        ];
    }
}
