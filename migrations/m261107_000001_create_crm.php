<?php

use yii\db\Migration;

/**
 * وحدة متابعة الزبائن (CRM) – المرحلة 1 و2.
 * جداول مستقلة تبدأ بـ crm ولا تعدّل جداول المبيعات أو الإيصالات أو العملاء.
 * الصلاحيات مفصّلة لكل أمر وتُضاف لدور «مدير» إن وُجد. آمن لإعادة التشغيل.
 */
class m261107_000001_create_crm extends Migration
{
    /** اسم الصلاحية => وصفها كما يظهر في شاشة الصلاحيات */
    const PERMISSIONS = [
        'crmMyTasks' => 'متابعة الزبائن: عرض مهامي',
        'crmAllTasks' => 'متابعة الزبائن: عرض مهام كل الموظفين',
        'crmTaskCreate' => 'متابعة الزبائن: إضافة مهمة',
        'crmTaskDone' => 'متابعة الزبائن: إغلاق مهمة',
        'crmTaskDelete' => 'متابعة الزبائن: حذف مهمة',
        'crmAssign' => 'متابعة الزبائن: تعيين المسؤول وتحويل المهام',
        'crmCardView' => 'متابعة الزبائن: عرض بطاقة الزبون',
        'crmNoteAdd' => 'متابعة الزبائن: إضافة ملاحظة',
        'crmBoardView' => 'متابعة الزبائن: عرض لوحة التحصيل',
        'crmStateChange' => 'متابعة الزبائن: نقل الزبون بين المراحل',
        'crmPromiseView' => 'متابعة الزبائن: عرض وعود الدفع',
        'crmPromiseCreate' => 'متابعة الزبائن: تسجيل وعد دفع',
        'crmPromiseEdit' => 'متابعة الزبائن: تعديل وعد دفع',
        'crmPromiseCancel' => 'متابعة الزبائن: إلغاء وعد دفع',
        'crmWhatsapp' => 'متابعة الزبائن: إرسال تذكير واتساب',
        'crmGroupView' => 'متابعة الزبائن: عرض المجموعات',
        'crmGroupEdit' => 'متابعة الزبائن: إنشاء وتعديل وحذف المجموعات',
        'crmGroupMembers' => 'متابعة الزبائن: إضافة وإزالة أعضاء المجموعة',
        'crmAlerts' => 'متابعة الزبائن: تنبيهات الوعود المنكوثة',
        'crmSettings' => 'متابعة الزبائن: الإعدادات',
    ];

    public function safeUp()
    {
        $opt = 'ENGINE=InnoDB';
        $has = fn($t) => $this->db->getTableSchema($t, true) !== null;

        if (!$has('crmSetting')) {
            $this->createTable('crmSetting', [
                'id' => $this->primaryKey(),
                'enabled' => $this->tinyInteger(1)->notNull()->defaultValue(0),
                'defaultUserId' => $this->integer()->null()->comment('المسؤول الافتراضي عن الزبائن بلا مسؤول'),
                'lastRunDate' => $this->date()->null()->comment('آخر يوم نُفّذ فيه التحديث اليومي'),
                'updated_at' => $this->dateTime()->null(),
            ], $opt);
            $this->insert('crmSetting', ['id' => 1, 'enabled' => 0]);
        }

        if (!$has('crmLevel')) {
            $this->createTable('crmLevel', [
                'id' => $this->primaryKey(),
                'name' => $this->string(100)->notNull(),
                'days' => $this->integer()->notNull()->comment('عدد الأيام منذ آخر دفعة'),
                'action' => $this->string(20)->notNull()->defaultValue('call')->comment('whatsapp, call, visit, manager'),
                'color' => $this->string(20)->notNull()->defaultValue('secondary'),
            ], $opt);
            $this->batchInsert('crmLevel', ['name', 'days', 'action', 'color'], [
                ['تذكير', 15, 'whatsapp', 'info'],
                ['اتصال', 30, 'call', 'warning'],
                ['زيارة ومطالبة', 60, 'visit', 'danger'],
                ['تصعيد', 90, 'manager', 'dark'],
            ]);
        }

        if (!$has('crmClient')) {
            $this->createTable('crmClient', [
                'clientId' => $this->integer()->notNull(),
                'userId' => $this->integer()->null()->comment('الموظف المسؤول'),
                'state' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0 عادي، 1 متنازع عليه، 2 مصعّد'),
                'levelId' => $this->integer()->null()->comment('المرحلة الحالية، تُحسب يومياً'),
                'balance' => $this->decimal(15, 3)->notNull()->defaultValue(0)->comment('لقطة الرصيد عند آخر تحديث'),
                'sinceDate' => $this->date()->null()->comment('تاريخ آخر دفعة، أو أقدم فاتورة آجلة'),
                'days' => $this->integer()->null(),
                'refreshed_at' => $this->dateTime()->null(),
            ], $opt);
            $this->addPrimaryKey('pk-crmClient', 'crmClient', 'clientId');
            $this->createIndex('idx-crmClient-user', 'crmClient', 'userId');
            $this->createIndex('idx-crmClient-level', 'crmClient', ['levelId', 'state']);
        }

        if (!$has('crmActivity')) {
            $this->createTable('crmActivity', [
                'id' => $this->primaryKey(),
                'clientId' => $this->integer()->notNull(),
                'type' => $this->string(20)->notNull()->defaultValue('call')->comment('whatsapp, call, visit, manager, task'),
                'dueDate' => $this->date()->notNull(),
                'userId' => $this->integer()->null()->comment('المكلّف'),
                'note' => $this->text()->null(),
                'auto' => $this->tinyInteger(1)->notNull()->defaultValue(0),
                'sourceKey' => $this->string(80)->null()->comment('يمنع تكرار المهام التلقائية'),
                'doneAt' => $this->dateTime()->null(),
                'doneBy' => $this->integer()->null(),
                'result' => $this->string(255)->null(),
                'created_by' => $this->integer()->null(),
                'created_at' => $this->dateTime()->notNull(),
            ], $opt);
            $this->createIndex('idx-crmActivity-open', 'crmActivity', ['doneAt', 'userId', 'dueDate']);
            $this->createIndex('idx-crmActivity-client', 'crmActivity', ['clientId', 'doneAt']);
            $this->createIndex('uq-crmActivity-source', 'crmActivity', 'sourceKey', true);
        }

        if (!$has('crmLog')) {
            $this->createTable('crmLog', [
                'id' => $this->primaryKey(),
                'clientId' => $this->integer()->notNull(),
                'kind' => $this->string(20)->notNull()->comment('note, whatsapp, task, promise, state, assign, level'),
                'text' => $this->text()->notNull(),
                'userId' => $this->integer()->null(),
                'created_at' => $this->dateTime()->notNull(),
            ], $opt);
            $this->createIndex('idx-crmLog-client', 'crmLog', ['clientId', 'created_at']);
        }

        if (!$has('crmPromise')) {
            $this->createTable('crmPromise', [
                'id' => $this->primaryKey(),
                'clientId' => $this->integer()->notNull(),
                'amount' => $this->decimal(15, 3)->notNull(),
                'promiseDate' => $this->date()->notNull(),
                'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0 قائم، 1 نُفّذ، 2 منكوث، 3 ملغى'),
                'note' => $this->string(255)->null(),
                'afterReceiptId' => $this->integer()->notNull()->defaultValue(0)->comment('آخر رقم إيصال عند تسجيل الوعد؛ يُحسب المقبوض بعده'),
                'paidSince' => $this->decimal(15, 3)->notNull()->defaultValue(0)->comment('المقبوض منذ تسجيل الوعد'),
                'reviewed' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('اطّلع المدير على الوعد المنكوث'),
                'created_by' => $this->integer()->null(),
                'created_at' => $this->dateTime()->notNull(),
                'closed_at' => $this->dateTime()->null(),
            ], $opt);
            $this->createIndex('idx-crmPromise-status', 'crmPromise', ['status', 'promiseDate']);
            $this->createIndex('idx-crmPromise-client', 'crmPromise', ['clientId', 'status']);
        }

        if (!$has('crmTag')) {
            $this->createTable('crmTag', [
                'id' => $this->primaryKey(),
                'name' => $this->string(100)->notNull(),
                'color' => $this->string(20)->notNull()->defaultValue('primary'),
                'created_at' => $this->dateTime()->notNull(),
            ], $opt);
        }

        if (!$has('crmClientTag')) {
            $this->createTable('crmClientTag', [
                'tagId' => $this->integer()->notNull(),
                'clientId' => $this->integer()->notNull(),
            ], $opt);
            $this->addPrimaryKey('pk-crmClientTag', 'crmClientTag', ['tagId', 'clientId']);
            $this->createIndex('idx-crmClientTag-client', 'crmClientTag', 'clientId');
        }

        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('مدير');
        foreach (self::PERMISSIONS as $name => $description) {
            $perm = $auth->getPermission($name);
            if ($perm === null) {
                $perm = $auth->createPermission($name);
                $perm->description = $description;
                $auth->add($perm);
            }
            if ($admin !== null && !$auth->hasChild($admin, $perm)) {
                $auth->addChild($admin, $perm);
            }
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach (array_keys(self::PERMISSIONS) as $name) {
            $perm = $auth->getPermission($name);
            if ($perm !== null) {
                $auth->remove($perm);
            }
        }
        foreach (['crmClientTag', 'crmTag', 'crmPromise', 'crmLog', 'crmActivity', 'crmClient', 'crmLevel', 'crmSetting'] as $t) {
            if ($this->db->getTableSchema($t, true) !== null) {
                $this->dropTable($t);
            }
        }
    }
}
