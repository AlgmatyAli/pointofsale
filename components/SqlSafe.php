<?php

namespace app\components;

/**
 * أدوات صغيرة لبناء استعلامات SQL الخام بأمان.
 * القيم المفردة تُمرَّر دائماً كـ parameters (:name)، أما قوائم IN (...) فتُنظَّف هنا
 * لتحتوي أرقاماً صحيحة فقط، فلا يمكن حقن SQL عبرها.
 */
class SqlSafe
{
    /**
     * يحوّل قيمة مثل "0,2" أو [1, 2] أو "5" إلى نص آمن من أرقام صحيحة مفصولة بفواصل.
     * أي جزء ليس رقماً صحيحاً يُتجاهَل. إن لم يبقَ شيء يعيد "0" حتى يبقى الاستعلام صالحاً.
     */
    public static function intList($value)
    {
        $parts = is_array($value) ? $value : explode(',', (string) $value);
        $ints = [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p !== '' && preg_match('/^-?\d+$/', $p)) {
                $ints[(int) $p] = (int) $p;
            }
        }
        return $ints ? implode(',', $ints) : '0';
    }
}
