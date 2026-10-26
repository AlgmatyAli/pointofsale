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
 * @property string|null $rateCompanies أرقام itemCompany التي تُطبَّق عليها نسبة الزيادة، فارغ = كل الأصناف
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

    /** @var array|null|false نسخة الطلب الحالي من صف بيانات الشركة (false = لم تُقرأ بعد) */
    private static $_currentRow = false;

    /**
     * صف بيانات الشركة كمصفوفة، من الكاش بدل استعلام في كل مرة.
     * بديل CompanyInfo::find()->asArray()->one(). يُمسح الكاش عند حفظ بيانات الشركة.
     */
    public static function currentArray(): ?array
    {
        if (self::$_currentRow === false) {
            self::$_currentRow = Yii::$app->cache->getOrSet(
                'company-info-row',
                fn() => static::find()->asArray()->one() ?: [],
                3600
            );
        }
        return self::$_currentRow ?: null;
    }

    /**
     * بيانات الشركة كـ Model للقراءة، بديل CompanyInfo::find()->one().
     * لا تستخدمه لتعديل البيانات وحفظها؛ استخدم findOne() في شاشة التعديل.
     */
    public static function current(): ?self
    {
        $row = static::currentArray();
        if ($row === null) {
            return null;
        }
        $model = static::instantiate($row);
        static::populateRecord($model, $row);
        $model->afterFind();
        return $model;
    }

    public static function clearCache(): void
    {
        self::$_currentRow = false;
        Yii::$app->cache->delete('company-info');
        Yii::$app->cache->delete('company-info-row');
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        static::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        static::clearCache();
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
            // Select2 متعدد يرسل مصفوفة، وتُحفظ أرقاماً مفصولة بفاصلة
            [['rateCompanies'], 'filter', 'filter' => function ($value) {
                $ids = array_values(array_unique(array_filter(array_map('intval', is_array($value) ? $value : explode(',', (string) $value)))));
                return $ids ? implode(',', $ids) : null;
            }],
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
            'rateCompanies' => Yii::t('app', 'الشركات التي تُطبَّق عليها النسبة'),
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
