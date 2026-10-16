<?php

use yii\db\Migration;

/**
 * يضيف عمود authKey لجدول user (للدخول التلقائي remember me)،
 * ويتأكد أن عمود password يتسع لـ 255 حرفاً (bcrypt = 60 حرفاً، md5 = 32).
 * آمن للتشغيل: لا يغيّر password إن كان مناسباً أصلاً.
 */
class m261013_000001_add_auth_key_to_user extends Migration
{
    public function safeUp()
    {
        $table = $this->db->getTableSchema('user', true);

        if (!isset($table->columns['authKey'])) {
            $this->addColumn('user', 'authKey', $this->string(64)->null());
        }

        $pw = $table->columns['password'] ?? null;
        if ($pw !== null && $pw->size !== null && (int)$pw->size < 255) {
            $this->alterColumn('user', 'password', $this->string(255)->null());
        }

        // تعبئة المفاتيح للمستخدمين الحاليين
        $ids = (new \yii\db\Query())->select('id')->from('user')
            ->where(['or', ['authKey' => null], ['authKey' => '']])->column($this->db);
        foreach ($ids as $id) {
            $this->update('user', ['authKey' => Yii::$app->security->generateRandomString(32)], ['id' => $id]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'authKey');
        // عمود password لا يُرجَع لحجم أصغر عمداً
    }
}
