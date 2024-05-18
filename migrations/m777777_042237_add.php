<?php

use yii\db\Migration;

/**
 * Class m777777_042237_add
 */
class m777777_042237_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

       // add "" permission 
       $createSalary = $auth->createPermission('createSalary');
       $createSalary->description = 'تسجيل المرتبات';
       $auth->add($createSalary);
 
        // add "" permission
        $updatSalary = $auth->createPermission('updateSalary');
        $updatSalary->description = 'تعديل المرتبات';
        $auth->add($updatSalary);

         // add "" permission
       $deleteSalary = $auth->createPermission('deleteSalary');
       $deleteSalary->description = 'حذف المرتبات';
       $auth->add($deleteSalary);

         // add "" permission
       $indexSalary = $auth->createPermission('indexSalary');
       $indexSalary->description = 'شاشة المرتبات';
       $auth->add($indexSalary);

       // add "" permission 
       $createEmployee = $auth->createPermission('createEmployee');
       $createEmployee->description = 'تسجيل بيانات الموظفين';
       $auth->add($createEmployee);

        // add "" permission
        $updateEmployee = $auth->createPermission('updatEmployee');
        $updateEmployee->description = 'تعديل بيانات الموظفين ';
        $auth->add($updateEmployee);

         // add "" permission
       $deleteEmployee = $auth->createPermission('deleteEmployee');
       $deleteEmployee->description = 'حذف بيانات الموظفين';
       $auth->add($deleteEmployee);

         // add "" permission
       $indexEmployee = $auth->createPermission('indexEmployee');
       $indexEmployee->description = 'شاشة الموظفين';
       $auth->add($indexEmployee);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_042237_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_042237_add cannot be reverted.\n";

        return false;
    }
    */
}
