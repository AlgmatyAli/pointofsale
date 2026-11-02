<?php

namespace app\models;

use Yii;

/**
 * سجل تعديلات فواتير المبيعات بعد حفظها (جدول salesAudit).
 *
 * @property int $id
 * @property int $salesId
 * @property int|null $billId
 * @property string $attribute
 * @property string|null $oldValue
 * @property string|null $newValue
 * @property int|null $user_id
 * @property string $created_at
 * @property int $reviewed
 * @property int|null $reviewed_by
 * @property string|null $reviewed_at
 *
 * @property Sales $sales
 * @property User $user
 */
class SalesAudit extends \yii\db\ActiveRecord
{
    /** الحقول المراقبة وأسماؤها في شاشة المراجعة */
    const LABELS = [
        'payWay' => 'طريقة الدفع',
        'clinet' => 'الزبون',
        'total' => 'إجمالي الفاتورة',
        'disscount' => 'الخصم',
        'paid' => 'المدفوع',
    ];

    public static function tableName()
    {
        return 'salesAudit';
    }

    /** يسجّل تغيير حقل في فاتورة محفوظة (القيم الرقمية المتساوية لا تُسجَّل) */
    public static function log(int $salesId, $billId, string $attribute, $old, $new): void
    {
        if (is_numeric($old) && is_numeric($new) && abs((float) $old - (float) $new) < 0.0005) {
            return;
        }
        if (!is_numeric($old) && (string) $old === (string) $new) {
            return;
        }
        $row = new self([
            'salesId' => $salesId,
            'billId' => $billId,
            'attribute' => $attribute,
            'oldValue' => $old === null ? null : (string) $old,
            'newValue' => $new === null ? null : (string) $new,
            'user_id' => Yii::$app->user->isGuest ? null : Yii::$app->user->id,
            'created_at' => date('Y-m-d H:i:s'),
            'reviewed' => 0,
        ]);
        $row->save(false);
    }

    /** عدد الفواتير التي فيها تعديلات لم يراجعها المدير */
    public static function pendingInvoices(): int
    {
        return (int) self::find()->where(['reviewed' => 0])->select('salesId')->distinct()->count();
    }

    /** القيمة كما تُعرض للمدير */
    public function display(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        if ($this->attribute === 'payWay') {
            $map = ($this->sales !== null && (int) $this->sales->type === 2)
                ? ['1' => 'نقدا', '2' => 'آجل']   // مسترجع مبيعات
                : ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'];
            return $map[$value] ?? $value;
        }
        if ($this->attribute === 'clinet') {
            $name = Client::find()->select('name')->where(['id' => (int) $value])->scalar();
            return $name !== false ? $name : $value;
        }
        return number_format((float) $value, 3);
    }

    public function getSales()
    {
        return $this->hasOne(Sales::class, ['id' => 'salesId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
