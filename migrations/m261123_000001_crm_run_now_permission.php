<?php

use yii\db\Migration;

/**
 * متابعة الزبائن: صلاحية «تحديث الآن» اليدوي (المهام والتحصيل والوعود) من كل شاشات المتابعة.
 * تُضاف لدور «مدير» إن وُجد. آمن لإعادة التشغيل.
 */
class m261123_000001_crm_run_now_permission extends Migration
{
    const NAME = 'crmRunNow';

    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $perm = $auth->getPermission(self::NAME);
        if ($perm === null) {
            $perm = $auth->createPermission(self::NAME);
            $perm->description = 'متابعة الزبائن: تحديث المهام والتحصيل والوعود يدوياً';
            $auth->add($perm);
        }
        $admin = $auth->getRole('مدير');
        if ($admin !== null && !$auth->hasChild($admin, $perm)) {
            $auth->addChild($admin, $perm);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $perm = $auth->getPermission(self::NAME);
        if ($perm !== null) {
            $auth->remove($perm);
        }
    }
}
