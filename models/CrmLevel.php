<?php

namespace app\models;

/**
 * مراحل المتابعة حسب عدد الأيام منذ آخر دفعة (على طريقة مستويات المتابعة في Odoo).
 *
 * @property int $id
 * @property string $name
 * @property int $days
 * @property string $action
 * @property string $color
 */
class CrmLevel extends \yii\db\ActiveRecord
{
    const ACTIONS = [
        'whatsapp' => 'تذكير واتساب',
        'call' => 'اتصال هاتفي',
        'visit' => 'زيارة',
        'manager' => 'تصعيد للمدير',
    ];

    const COLORS = [
        'info' => 'أزرق فاتح',
        'primary' => 'أزرق',
        'warning' => 'أصفر',
        'danger' => 'أحمر',
        'dark' => 'أسود',
        'secondary' => 'رمادي',
        'success' => 'أخضر',
    ];

    public static function tableName()
    {
        return 'crmLevel';
    }

    public function rules()
    {
        return [
            [['name', 'days', 'action', 'color'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['days'], 'integer', 'min' => 1, 'max' => 3650],
            [['days'], 'unique'],
            [['action'], 'in', 'range' => array_keys(self::ACTIONS)],
            [['color'], 'in', 'range' => array_keys(self::COLORS)],
        ];
    }

    public function attributeLabels()
    {
        return ['name' => 'اسم المرحلة', 'days' => 'بعد كم يوم بدون دفع', 'action' => 'الإجراء', 'color' => 'اللون'];
    }

    /** @return self[] مرتبة تصاعدياً بالأيام */
    public static function ordered(): array
    {
        return static::find()->orderBy(['days' => SORT_ASC])->all();
    }

    /** أعلى مرحلة بلغها الزبون، أو null إن لم يبلغ أول مرحلة */
    public static function forDays(?int $days, array $levels): ?self
    {
        $found = null;
        if ($days === null) {
            return null;
        }
        foreach ($levels as $level) {
            if ($days >= (int) $level->days) {
                $found = $level;
            }
        }
        return $found;
    }
}
