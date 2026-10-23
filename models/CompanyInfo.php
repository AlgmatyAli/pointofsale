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

    /** ألوان Bootstrap الأساسية المتاحة للقائمة الجانبية: القيمة المحفوظة => [الاسم, اللون] */
    const SKINS = [
        'primary' => ['أزرق (Primary)', '#0d6efd'],
        'secondary' => ['رمادي (Secondary)', '#6c757d'],
        'success' => ['أخضر (Success)', '#198754'],
        'danger' => ['أحمر (Danger)', '#dc3545'],
        'warning' => ['أصفر (Warning)', '#ffc107'],
        'info' => ['سماوي (Info)', '#0dcaf0'],
        'light' => ['فاتح (Light)', '#f8f9fa'],
        'dark' => ['داكن (Dark)', '#212529'],
    ];

    /** ألوان قيم skin القديمة (AdminLTE 2) لتبقى البيانات المحفوظة سابقاً تعمل */
    const LEGACY_SKINS = [
        'skin-blue' => '#1e3a5f',
        'skin-blue-light' => '#e8f1f8',
        'skin-yellow' => '#6b4a0a',
        'skin-yellow-light' => '#fdf3e1',
        'skin-green' => '#0f4d33',
        'skin-green-light' => '#e5f6ee',
        'skin-red' => '#6e1f17',
        'skin-red-light' => '#fbeae8',
    ];

    /**
     * يحوّل قيمة skin إلى كود لون #rrggbb، أو نص فارغ للون الافتراضي.
     */
    public static function skinColor(?string $skin): string
    {
        $skin = trim((string) $skin);
        return self::SKINS[$skin][1] ?? self::LEGACY_SKINS[$skin] ?? '';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('company-info');
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->cache->delete('company-info');
    }

    /**
     * {@inheritdoc}
     */
    public $file;
    public function rules()
    {
        return [
            [['name', 'work', 'address', 'phone1', 'phone2', 'phone3', 'fax', 'email', 'searchById', 'repeatCategory', 'payWayCash'], 'required'],
            [['rate'], 'number'],
            [['address', 'terms'], 'string'],
            [['currancy', 'searchById', 'repeatCategory', 'payWayCash', 'invoiceState', 'waitQnty', 'criteriaـvalue', 'zeroQnty', 'avgCost', 'editSalePriceOnPurchaseUpdate'], 'integer'],
            [['name', 'work'], 'string', 'max' => 150],
            [['phone1', 'phone2', 'phone3', 'fax'], 'string', 'max' => 14],
            [['email'], 'string', 'max' => 20],
            [['path'], 'string', 'max' => 255],
            [['skin'], 'string', 'max' => 100],
            [['skin'], 'in', 'range' => array_keys(self::SKINS), 'message' => 'اختر لوناً من القائمة'],
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
            'zeroQnty' => Yii::t('app', 'Zero Qnty'),
            'avgCost' => Yii::t('app', 'Average Cost'),
            'editSalePriceOnPurchaseUpdate' => Yii::t('app', 'Edit Sale Price on Purchase Update'),
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
