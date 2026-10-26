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
        if ($this->db->getTableSchema('itemCompany', true) === null) {
            $this->createTable('itemCompany', [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull()->unique()->comment('اسم الشركة'),
                'created_at' => $this->dateTime()->null(),
                'update_at' => $this->dateTime()->null(),
            ], 'CHARACTER SET utf8mb4 ENGINE=InnoDB');
        }

        $this->execute("
            INSERT INTO itemCompany (name, created_at)
            SELECT DISTINCT TRIM(c.company), NOW()
            FROM category c
            WHERE c.company IS NOT NULL AND TRIM(c.company) <> ''
              AND NOT EXISTS (SELECT 1 FROM itemCompany ic WHERE ic.name = TRIM(c.company))
        ");

        $category = $this->db->getTableSchema('category', true);
        if (!isset($category->columns['companyId'])) {
            $this->addColumn('category', 'companyId', $this->integer()->null()->after('company')->comment('itemCompany.id'));
            $this->createIndex('idx-category-companyId', 'category', 'companyId');
            // RESTRICT: لا يمكن حذف شركة مرتبطة بأصناف
            $this->addForeignKey('fk-category-companyId', 'category', 'companyId', 'itemCompany', 'id', 'RESTRICT', 'CASCADE');
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
