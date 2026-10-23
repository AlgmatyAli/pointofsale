<?php

namespace app\components;

use Yii;
use yii\web\UploadedFile;

/**
 * فحص الملفات المرفوعة قبل حفظها على القرص.
 * بدون هذا الفحص يمكن رفع ملف .php إلى مجلد web/img ثم تشغيله من المتصفح.
 */
class UploadGuard
{
    const IMAGE = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    const EXCEL = ['xls', 'xlsx'];

    /**
     * يعيد true إن كان الملف من الامتدادات المسموحة وحجمه مقبول (وصورة حقيقية إن كان صورة).
     * عند الرفض يضع رسالة خطأ في الجلسة ويعيد false.
     */
    public static function isAllowed($file, array $extensions, $maxBytes = 5242880)
    {
        $ok = $file instanceof UploadedFile
            && $file->error === UPLOAD_ERR_OK
            && in_array(strtolower((string) $file->extension), $extensions, true)
            && $file->size > 0 && $file->size <= $maxBytes;

        if ($ok && array_intersect([strtolower($file->extension)], self::IMAGE)) {
            $ok = @getimagesize($file->tempName) !== false;
        }

        if (!$ok && $file instanceof UploadedFile) {
            Yii::$app->session->setFlash('error', 'الملف المرفوع غير مقبول. الأنواع المسموحة: ' . implode(', ', $extensions));
        }
        return $ok;
    }
}
