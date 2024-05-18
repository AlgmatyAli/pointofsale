<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_info".
 *
 * @property int $id
 * @property string $name
 * @property string $work
 * @property string $address
 * @property string $phone1
 * @property string $phone2
 * @property string $phone3
 * @property string $fax
 * @property string $email
 * @property string|null $terms
 * @property string|null $path
 * @property int|null $currancy
 * @property string|null $skin
 * @property int $searchById
 * @property int $repeatCategory
 * @property int $payWayCash
 * @property int|null $invoiceState
 *
 * @property Currancy $currancy0
 */
class CompanyInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_info';
    }

    /**
     * {@inheritdoc}
     */
    public $file ;
    public function rules()
    {
        return [
            [['name', 'work', 'address', 'phone1', 'phone2', 'phone3', 'fax', 'email', 'searchById', 'repeatCategory', 'payWayCash'], 'required'],
            [['rate'], 'number'],
            [['address', 'terms'], 'string'],
            [['currancy', 'searchById', 'repeatCategory', 'payWayCash', 'invoiceState', 'waitQnty', 'criteriaـvalue'], 'integer'],
            [['name', 'work'], 'string', 'max' => 150],
            [['phone1', 'phone2', 'phone3', 'fax'], 'string', 'max' => 14],
            [['email'], 'string', 'max' => 20],
            [['path'], 'string', 'max' => 255],
            [['skin'], 'string', 'max' => 100],
            [['file'], 'file'],
            [['currancy'], 'exist', 'skipOnError' => true, 'targetClass' => Currancy::className(), 'targetAttribute' => ['currancy' => 'id']],
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
            'work' => Yii::t('app', 'Work'),
            'address' => Yii::t('app', 'Address'),
            'phone1' => Yii::t('app', 'Phone1'),
            'phone2' => Yii::t('app', 'Phone2'),
            'phone3' => Yii::t('app', 'Phone3'),
            'fax' => Yii::t('app', 'Fax'),
            'email' => Yii::t('app', 'Email'),
            'terms' => Yii::t('app', 'Terms'),
            'path' => Yii::t('app', 'Path'),
            'currancy' => Yii::t('app', 'Currancy'),
            'skin' => Yii::t('app', 'Skin'),
            'searchById' => Yii::t('app', 'Search By ID'),
            'repeatCategory' => Yii::t('app', 'Repeat Category'),
            'payWayCash' => Yii::t('app', 'Pay Way Cash'),
            'invoiceState' => Yii::t('app', 'Invoice State'),
            'waitQnty' => Yii::t('app', 'Wait Qnty'),
        ];
    }

    /**
     * Gets query for [[Currancy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::className(), ['id' => 'currancy']);
    }
}
