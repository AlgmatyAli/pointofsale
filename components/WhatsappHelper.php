<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

/**
 * أدوات إرسال الفواتير عبر واتساب:
 *  - تنظيف رقم الهاتف الليبي إلى الصيغة الدولية 218XXXXXXXXX؛
 *  - رابط PDF موقَّع (HMAC) ينتهي بعد مدة، فلا يمكن تخمين فواتير الآخرين ولا تغيير رقم الفاتورة في الرابط؛
 *  - رابط wa.me بنص جاهز.
 * المفتاح السري هو cookieValidationKey الموجود في إعدادات التطبيق، فلا حاجة لعمود جديد في قاعدة البيانات.
 */
class WhatsappHelper
{
    /** مدة صلاحية رابط الفاتورة (ثوانٍ): 30 يوماً */
    const LINK_TTL = 2592000;

    /** يعيد الرقم بصيغة 218912345678 أو null إن لم يكن صالحاً */
    public static function normalizePhone($raw)
    {
        $d = preg_replace('/\D+/', '', (string) $raw);
        if ($d === '') {
            return null;
        }
        if (strpos($d, '00') === 0) {
            $d = substr($d, 2);
        }
        if (strpos($d, '218') === 0) {
            // خطأ شائع: 2180912345678 (صفر بعد رمز الدولة)
            if (preg_match('/^2180(\d{9})$/', $d, $m)) {
                $d = '218' . $m[1];
            }
        } elseif ($d[0] === '0') {
            $d = '218' . substr($d, 1);
        } elseif (strlen($d) === 9) {
            $d = '218' . $d;
        }
        return preg_match('/^218\d{9}$/', $d) ? $d : null;
    }

    private static function signature($id, $exp)
    {
        return hash_hmac('sha256', 'sales-pdf|' . (int) $id . '|' . (int) $exp, (string) Yii::$app->request->cookieValidationKey);
    }

    /** رابط مطلق لملف PDF الفاتورة (يفتح بلا تسجيل دخول) */
    public static function invoiceUrl($id)
    {
        $exp = time() + self::LINK_TTL;
        return Url::to(['sales/invoice-pdf', 'id' => (int) $id, 'exp' => $exp, 'sig' => self::signature($id, $exp)], true);
    }

    public static function verify($id, $exp, $sig)
    {
        if (!ctype_digit((string) $exp) || (int) $exp < time()) {
            return false;
        }
        return hash_equals(self::signature($id, (int) $exp), (string) $sig);
    }

    public static function waUrl($phone, $text)
    {
        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($text);
    }

    public static function money($v)
    {
        return number_format((float) $v, 3, '.', ',');
    }
}
