<?php

namespace app\models\base;

use Yii;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "reorderitems".
 *
 * @property integer $id
 * @property string $name
 * @property string $serialNo
 * @property integer $minimum
 * @property double $quantity
 * @property string $class
 * @property string $company
 */
class Reorderitems extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'minimum'], 'integer'],
            [['quantity'], 'number'],
            [['name', 'serialNo', 'class', 'company'], 'string', 'max' => 255],
            [['lock'], 'default', 'value' => '0'],
            [['lock'], 'mootensai\components\OptimisticLockValidator']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reorderItems';
    }

    /**
     *
     * @return string
     * overwrite function optimisticLock
     * return string name of field are used to stored optimistic lock
     *
     */
    public function optimisticLock() {
        return 'lock';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'serialNo' => Yii::t('app', 'Serial No'),
            'minimum' => Yii::t('app', 'Minimum'),
            'quantity' => Yii::t('app', 'Quantity'),
            'class' => Yii::t('app', 'Class'),
            'company' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'uuid' => [
                'class' => UUIDBehavior::className(),
                'column' => 'id',
            ],
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\ReorderitemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\ReorderitemsQuery(get_called_class());
    }
}
