<?php

use yii\db\Migration;

/**
 * جدول الشركات المصنّعة للأصناف (itemCompany) وربطه بالأصناف عبر category.companyId.
 *  - ينقل أسماء الشركات الموجودة حالياً في category.company (بدون تكرار ولا فراغات).
 *  - عمود category.company (النص) يبقى كما هو لأن التقارير والـ Views تعتمد عليه، ويُحدَّث تلقائياً من الموديل.
 * آمن لإعادة التشغيل.
 */
class m261025_000001_create_item_company extends Migration
{
    public function safeUp()
    {
        // نفس ترميز عمود category.company، وإلا رفض MySQL مقارنة الأسماء بين الجدولين
        // (Illegal mix of collations) عندما يختلف الترميز الافتراضي للسيرفر عن ترميز الجداول القديمة
        $collation = $this->db->createCommand(
            "SELECT COLLATION_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'category' AND COLUMN_NAME = 'company'"
        )->queryScalar() ?: 'utf8mb4_general_ci';
        $collation = preg_replace('/[^a-z0-9_]/i', '', $collation);
        $charset = strstr($collation, '_', true);

        if ($this->db->getTableSchema('itemCompany', true) === null) {
            $this->createTable('itemCompany', [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull()->unique()->comment('اسم الشركة'),
                'created_at' => $this->dateTime()->null(),
                'update_at' => $this->dateTime()->null(),
            ], "CHARACTER SET $charset COLLATE $collation ENGINE=InnoDB");
        } else {
            // جدول أُنشئ في محاولة سابقة بترميز السيرفر الافتراضي
            $current = $this->db->createCommand(
                "SELECT COLLATION_NAME FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'itemCompany' AND COLUMN_NAME = 'name'"
            )->queryScalar();
            if ($current !== $collation) {
                $this->execute("ALTER TABLE itemCompany CONVERT TO CHARACTER SET $charset COLLATE $collation");
            }
        }

        // IGNORE: أسماء تختلف فقط بحالة الأحرف أو بتشكيل عربي (مثل 'ِAISIN' و 'AISIN') تُعتبر نفس الاسم
        // في الفهرس الفريد، فيُحفظ أولها ويُتجاهل الباقي، ثم تُربط كل أصنافها بنفس الشركة في الخطوة التالية
        $this->execute("
            INSERT IGNORE INTO itemCompany (name, created_at)
            SELECT DISTINCT TRIM(c.company), NOW()
            FROM category c
            WHERE c.company IS NOT NULL AND TRIM(c.company) <> ''
              AND NOT EXISTS (SELECT 1 FROM itemCompany ic WHERE ic.name = TRIM(c.company))
        ");

        // كل خطوة تُفحص وحدها حتى يكمل التشغيل من حيث توقف إن فشل سابقاً
        $category = $this->db->getTableSchema('category', true);
        if (!isset($category->columns['companyId'])) {
            $this->addColumn('category', 'companyId', $this->integer()->null()->after('company')->comment('itemCompany.id'));
        }
        $hasIndex = (bool) $this->db->createCommand("SHOW INDEX FROM category WHERE Key_name = 'idx-category-companyId'")->queryOne();
        if (!$hasIndex) {
            $this->createIndex('idx-category-companyId', 'category', 'companyId');
        }
        $hasFk = (bool) $this->db->createCommand(
            "SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'category' AND CONSTRAINT_NAME = 'fk-category-companyId'"
        )->queryScalar();
        if (!$hasFk) {
            // مع foreign_key_checks = 0 يضيف MySQL المفتاح دون إعادة بناء جدول category،
            // فلا يرفضه بسبب تواريخ قديمة '0000-00-00' في created_at. companyId كله فارغ هنا فلا يوجد ما يُفحص.
            $this->execute('SET foreign_key_checks = 0');
            try {
                // RESTRICT: لا يمكن حذف شركة مرتبطة بأصناف
                $this->addForeignKey('fk-category-companyId', 'category', 'companyId', 'itemCompany', 'id', 'RESTRICT', 'CASCADE');
            } finally {
                $this->execute('SET foreign_key_checks = 1');
            }
        }

        $this->execute("
            UPDATE category c
            JOIN itemCompany ic ON ic.name = TRIM(c.company)
            SET c.companyId = ic.id, c.company = ic.name
            WHERE c.companyId IS NULL
        ");
    }

    public function safeDown()
    {
        $category = $this->db->getTableSchema('category', true);
        if (isset($category->columns['companyId'])) {
            $this->dropForeignKey('fk-category-companyId', 'category');
            $this->dropIndex('idx-category-companyId', 'category');
            $this->dropColumn('category', 'companyId');
        }
        if ($this->db->getTableSchema('itemCompany', true) !== null) {
            $this->dropTable('itemCompany');
        }
    }
}
