<?php

use yii\db\Migration;

/**
 * Class m190127_093518_add_auth_itemes
 */
class m190127_093518_add_auth_itemes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole('مدير');
        $admin->type = 1;
        $auth->add($admin);

        $helper = $auth->createRole('مساعد');
        $helper->type = 3;
        $auth->add($helper);

        // add "" permission 
        $createClient = $auth->createPermission('createClient');
        $createClient->description = 'تسجيل العملاء';
        $auth->add($createClient);

        // add "" permission
        $updatClient = $auth->createPermission('updatClient');
        $updatClient->description = 'تعديل عميل';
        $auth->add($updatClient);

        // add "" permission
        $deleteClient = $auth->createPermission('deleteClient');
        $deleteClient->description = 'حذف عميل';
        $auth->add($deleteClient);

        // add "" permission
        $indexClient = $auth->createPermission('indexClient');
        $indexClient->description = 'تقرير العملاء';
        $auth->add($indexClient);

        // add "" permission
        $clientDepts = $auth->createPermission('clientDepts');
        $clientDepts->description = 'تقرير بإجمالي الديون';
        $auth->add($clientDepts);

        // add "" permission
        $clientHistrans = $auth->createPermission('clientHistrans');
        $clientHistrans->description = 'عرض كشف حساب عميل';
        $auth->add($clientHistrans);

        // add "" permission
        $createUsers = $auth->createPermission('createUsers');
        $createUsers->description = 'تسجيل مستخدم';
        $auth->add($createUsers);

        // add "" permission
        $updateUsers = $auth->createPermission('updateUsers');
        $updateUsers->description = 'تعديل مستخدم';
        $auth->add($updateUsers);

        // add "" permission
        $deleteUsers = $auth->createPermission('deleteUsers');
        $deleteUsers->description = 'حدف مستخدم';
        $auth->add($deleteUsers);

        // add "" permission
        $indexUsers = $auth->createPermission('indexUsers');
        $indexUsers->description = 'شاشة المستخدمين';
        $auth->add($indexUsers);

        // add "" permission
        $changePassword = $auth->createPermission('changePassword');
        $changePassword->description = 'تعديل كلمة المرور';
        $auth->add($changePassword);

        // add "" permission
        $companyInfo = $auth->createPermission('companyInfo');
        $companyInfo->description = 'ادارة بيانات المؤسسة';
        $auth->add($companyInfo);

        // add "" permission
        $createBranches = $auth->createPermission('createBranches');
        $createBranches->description = 'تسجيل فرع';
        $auth->add($createBranches);

        // add "" permission
        $updateBranches = $auth->createPermission('updateBranches');
        $updateBranches->description = 'تعديل فرع';
        $auth->add($updateBranches);

        // add "" permission
        $deleteBranches = $auth->createPermission('deleteBranches');
        $deleteBranches->description = 'حدف فرع';
        $auth->add($deleteBranches);

        // add "" permission
        $indexBranches = $auth->createPermission('indexBranches');
        $indexBranches->description = 'شاشة الفروع';
        $auth->add($indexBranches);

        // add "" permission
        $createItems = $auth->createPermission('createItems');
        $createItems->description = 'تسجيل بنود المصاريف';
        $auth->add($createItems);

        // add "" permission
        $updateItems = $auth->createPermission('updateItems');
        $updateItems->description = 'تعديل بنود المصاريف';
        $auth->add($updateItems);

        // add "" permission
        $deleteItems = $auth->createPermission('deleteItems');
        $deleteItems->description = 'حدف بنود المصاريف';
        $auth->add($deleteItems);

        // add "" permission
        $indexItems = $auth->createPermission('indexItems');
        $indexItems->description = 'شاشة بنود المصاريف';
        $auth->add($indexItems);

        // add "" permission
        $createCategory = $auth->createPermission('createCategory');
        $createCategory->description = 'تسجيل الأصناف';
        $auth->add($createCategory);

        // add "" permission
        $updateCategory = $auth->createPermission('updateCategory');
        $updateCategory->description = 'تعديل الأصناف';
        $auth->add($updateCategory);

        // add "" permission
        $deleteCategory = $auth->createPermission('deleteCategory');
        $deleteCategory->description = 'حدف الأصناف';
        $auth->add($deleteCategory);

        // add "" permission
        $indexCategory = $auth->createPermission('indexCategory');
        $indexCategory->description = 'شاشة الأصناف';
        $auth->add($indexCategory);

        // add "" permission
        $categoryHistrans = $auth->createPermission('categoryHistrans');
        $categoryHistrans->description = 'كشف حساب صنف';
        $auth->add($categoryHistrans);

        // add "" permission
        $reOrder = $auth->createPermission('reOrder');
        $reOrder->description = 'تقرير بالحد الأدنى للأصناف';
        $auth->add($reOrder);

        // add "" permission
        $moreRequest = $auth->createPermission('moreRequest');
        $moreRequest->description = 'تقرير بالأصناف الأكثر رواجا';
        $auth->add($moreRequest);

        // add "" permission
        $reOrder = $auth->createPermission('upload');
        $reOrder->description = 'تحميل الأصناف من ملف إكسل';
        $auth->add($reOrder);

        // add "" permission
        $barcodePrint = $auth->createPermission('barcodePrint');
        $barcodePrint->description = 'انشاء وطباعة الباركود';
        $auth->add($barcodePrint);

        // add "" permission
        $info = $auth->createPermission('info');
        $info->description = 'بطاعة معلومات صنف';
        $auth->add($info);

        // add "" permission
        $createExpenses = $auth->createPermission('createExpenses');
        $createExpenses->description = 'تسجيل المصروفات';
        $auth->add($createExpenses);

        // add "" permission
        $updateExpenses = $auth->createPermission('updateExpenses');
        $updateExpenses->description = 'تعديل المصروفات';
        $auth->add($updateExpenses);

        // add "" permission
        $deleteExpenses = $auth->createPermission('deleteExpenses');
        $deleteExpenses->description = 'حذف المصروفات';
        $auth->add($deleteExpenses);

        // add "" permission
        $indexExpenses = $auth->createPermission('indexExpenses');
        $indexExpenses->description = 'تقرير عن المصروفات';
        $auth->add($indexExpenses);

        // add "" permission
        $createReciept = $auth->createPermission('createReciept');
        $createReciept->description = 'تسجيل ايصالات  ';
        $auth->add($createReciept);
        // add "" permission
        $updateReciept = $auth->createPermission('updateReciept');
        $updateReciept->description = 'تعديل ايصالات ';
        $auth->add($updateReciept);
        // add "" permission
        $deleteReciept = $auth->createPermission('deleteReciept');
        $deleteReciept->description = 'حذف ايصالات ';
        $auth->add($deleteReciept);
        // add "" permission
        $indexReciept = $auth->createPermission('indexReciept');
        $indexReciept->description = 'تقرير عن ايصالات ';
        $auth->add($indexReciept);

        // add "" permission
        $inventory = $auth->createPermission('inventory');
        $inventory->description = 'تقرير بالجرد';
        $auth->add($inventory);

        // add "" permission
        $prices = $auth->createPermission('prices');
        $prices->description = 'قائمة الأسعـار';
        $auth->add($prices);

        // add "" permission
        $createTransferItems = $auth->createPermission('createTransferItems');
        $createTransferItems->description = 'انشاء نقل الاصناف بين الفروع';
        $auth->add($createTransferItems);

        // add "" permission
        $updateTransferItems = $auth->createPermission('updateTransferItems');
        $updateTransferItems->description = 'تعديل نقل الاصناف بين الفروع';
        $auth->add($updateTransferItems);

        // add "" permission
        $deleteTransferItems = $auth->createPermission('deleteTransferItems');
        $deleteTransferItems->description = 'الغاء نقل الاصناف بين الفروع';
        $auth->add($deleteTransferItems);

        // add "" permission
        $indexTransferItems = $auth->createPermission('indexTransferItems');
        $indexTransferItems->description = 'تقرير نقل الاصناف بين الفروع';
        $auth->add($indexTransferItems);

        // add "" permission
        $createReorder = $auth->createPermission('createReorder');
        $createReorder->description = 'انشاء طلبية مشتريات';
        $auth->add($createReorder);

        // add "" permission
        $updateReorder = $auth->createPermission('updateReorder');
        $updateReorder->description = 'تعديل طلبية مشتريات';
        $auth->add($updateReorder);

        // add "" permission
        $deleteReorder = $auth->createPermission('deleteReorder');
        $deleteReorder->description = 'الغاء طلبية مشتريات';
        $auth->add($deleteReorder);

        // add "" permission
        $indexReorder = $auth->createPermission('indexReorder');
        $indexReorder->description = 'تقرير طلبيات المشتريات';
        $auth->add($indexReorder);

        // add "" permission
        $createArrangment = $auth->createPermission('createArrangment');
        $createArrangment->description = 'انشاء تسوية جرد';
        $auth->add($createArrangment);

        // add "" permission
        $updateArrangment = $auth->createPermission('updateArrangment');
        $updateArrangment->description = 'تعديل تسوية جرد';
        $auth->add($updateArrangment);

        // add "" permission
        $deleteArrangment = $auth->createPermission('deleteArrangment');
        $deleteArrangment->description = 'الغاء تسوية جرد';
        $auth->add($deleteArrangment);

        // add "" permission
        $indexArrangment = $auth->createPermission('indexArrangment');
        $indexArrangment->description = 'تقرير تسوية جرد';
        $auth->add($indexArrangment);

        // add "" permission
        $createPurchases = $auth->createPermission('createPurchases');
        $createPurchases->description = 'تسجيل المشتريات';
        $auth->add($createPurchases);

        // add "" permission
        $updatePurchases = $auth->createPermission('updatePurchases');
        $updatePurchases->description = 'تعديل المشتريات';
        $auth->add($updatePurchases);

        // add "" permission
        $deletePurchases = $auth->createPermission('deletePurchases');
        $deletePurchases->description = 'الغاء المشتريات';
        $auth->add($deletePurchases);

        // add "" permission
        $indexPurchases = $auth->createPermission('indexPurchases');
        $indexPurchases->description = 'تقرير عن المشتريات';
        $auth->add($indexPurchases);

        // add "" permission
        $SavePurchasesAsNew = $auth->createPermission('SavePurchasesAsNew');
        $SavePurchasesAsNew->description = 'نسخ فاتورة المشتريات';
        $auth->add($SavePurchasesAsNew);

        // add "" permission
        $createSales = $auth->createPermission('createSales');
        $createSales->description = 'تسجيل المبيعات';
        $auth->add($createSales);

        // add "" permission
        $updateSales = $auth->createPermission('updateSales');
        $updateSales->description = 'تعديل المبيعات';
        $auth->add($updateSales);

        // add "" permission
        $deleteSales = $auth->createPermission('deleteSales');
        $deleteSales->description = 'حذف المبيعات';
        $auth->add($deleteSales);

        // add "" permission
        $indexSales = $auth->createPermission('indexSales');
        $indexSales->description = 'تقرير عن المبيعات';
        $auth->add($indexSales);

        // add "" permission
        $ftran = $auth->createPermission('ftran');
        $ftran->description = 'تقرير الحركة اليومية';
        $auth->add($ftran);

        // add "" permission
        $profit = $auth->createPermission('profit');
        $profit->description = 'تقرير عن الأرباح اليومية';
        $auth->add($profit);

        // add "" permission
        $saveSalesAsNew = $auth->createPermission('saveSalesAsNew');
        $saveSalesAsNew->description = 'نسخ فاتورة المبيعات';
        $auth->add($saveSalesAsNew);

        // add "" permission
        $createBackPurchase = $auth->createPermission('createBackSales');
        $createBackPurchase->description = 'تسجيل مسترجع مبيعات';
        $auth->add($createBackPurchase);


        // add "" permission
        $createSafe = $auth->createPermission('createSafe');
        $createSafe->description = 'تسجيل حركة الخزينة';
        $auth->add($createSafe);

        // add "" permission
        $updateSafe = $auth->createPermission('updateSafe');
        $updateSafe->description = 'تعديل حركة الخزينة';
        $auth->add($updateSafe);

        // add "" permission
        $deleteSafe = $auth->createPermission('deleteSafe');
        $deleteSafe->description = 'حذف حركة خزينة';
        $auth->add($deleteSafe);

        // add "" permission
        $indexSafe = $auth->createPermission('indexSafe');
        $indexSafe->description = 'تقرير حركة الخزينة';
        $auth->add($indexSafe);

        // add "" permission
        $createTransfer = $auth->createPermission('createTransfer');
        $createTransfer->description = 'تسجيل المقاصة';
        $auth->add($createTransfer);

        // add "" permission
        $updateTransfer = $auth->createPermission('updateTransfer');
        $updateTransfer->description = 'تعديل المقاصة';
        $auth->add($updateTransfer);

        // add "" permission
        $deleteTransfer = $auth->createPermission('deleteTransfer');
        $deleteTransfer->description = 'حذف المقاصة';
        $auth->add($deleteTransfer);

        // add "" permission
        $indexTransfer = $auth->createPermission('indexTransfer');
        $indexTransfer->description = 'تقرير عن المقاصة';
        $auth->add($indexTransfer);

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

        // add "" permission 
        $userCanSeeStockLessThanZero = $auth->createPermission('userCanSeeStockLessThanZero');
        $userCanSeeStockLessThanZero->description = 'عرض الكميات الاقل من الصفر';
        $auth->add($userCanSeeStockLessThanZero);

        // Assign permissions to roles
        $auth->addChild($admin, $createClient);
        $auth->addChild($admin, $updatClient);
        $auth->addChild($admin, $deleteClient);
        $auth->addChild($admin, $indexClient);
        $auth->addChild($admin, $clientDepts);
        $auth->addChild($admin, $clientHistrans);
        $auth->addChild($admin, $createUsers);
        $auth->addChild($admin, $updateUsers);
        $auth->addChild($admin, $deleteUsers);
        $auth->addChild($admin, $indexUsers);
        $auth->addChild($admin, $changePassword);
        $auth->addChild($admin, $companyInfo);
        $auth->addChild($admin, $createBranches);
        $auth->addChild($admin, $updateBranches);
        $auth->addChild($admin, $deleteBranches);
        $auth->addChild($admin, $indexBranches);
        $auth->addChild($admin, $createItems);
        $auth->addChild($admin, $updateItems);
        $auth->addChild($admin, $deleteItems);
        $auth->addChild($admin, $indexItems);
        $auth->addChild($admin, $createCategory);
        $auth->addChild($admin, $updateCategory);
        $auth->addChild($admin, $deleteCategory);
        $auth->addChild($admin, $indexCategory);
        $auth->addChild($admin, $categoryHistrans);
        $auth->addChild($admin, $reOrder);
        $auth->addChild($admin, $moreRequest);
        $auth->addChild($admin, $barcodePrint);
        $auth->addChild($admin, $info);
        $auth->addChild($admin, $createExpenses);
        $auth->addChild($admin, $updateExpenses);
        $auth->addChild($admin, $deleteExpenses);
        $auth->addChild($admin, $indexExpenses);
        $auth->addChild($admin, $createReciept);
        $auth->addChild($admin, $updateReciept);
        $auth->addChild($admin, $deleteReciept);
        $auth->addChild($admin, $indexReciept);
        $auth->addChild($admin, $inventory);
        $auth->addChild($admin, $prices);
        $auth->addChild($admin, $createTransferItems);
        $auth->addChild($admin, $updateTransferItems);
        $auth->addChild($admin, $deleteTransferItems);
        $auth->addChild($admin, $indexTransferItems);
        $auth->addChild($admin, $createReorder);
        $auth->addChild($admin, $updateReorder);
        $auth->addChild($admin, $deleteReorder);
        $auth->addChild($admin, $indexReorder);
        $auth->addChild($admin, $createArrangment);
        $auth->addChild($admin, $updateArrangment);
        $auth->addChild($admin, $deleteArrangment);
        $auth->addChild($admin, $indexArrangment);
        $auth->addChild($admin, $createPurchases);
        $auth->addChild($admin, $updatePurchases);
        $auth->addChild($admin, $deletePurchases);
        $auth->addChild($admin, $indexPurchases);
        $auth->addChild($admin, $SavePurchasesAsNew);
        $auth->addChild($admin, $createSales);
        $auth->addChild($admin, $updateSales);
        $auth->addChild($admin, $deleteSales);
        $auth->addChild($admin, $indexSales);
        $auth->addChild($admin, $ftran);
        $auth->addChild($admin, $profit);
        $auth->addChild($admin, $saveSalesAsNew);
        $auth->addChild($admin, $createBackPurchase);
        $auth->addChild($admin, $createSafe);
        $auth->addChild($admin, $updateSafe);
        $auth->addChild($admin, $deleteSafe);
        $auth->addChild($admin, $indexSafe);
        $auth->addChild($admin, $createTransfer);
        $auth->addChild($admin, $updateTransfer);
        $auth->addChild($admin, $deleteTransfer);
        $auth->addChild($admin, $indexTransfer);
        $auth->addChild($admin, $createSalary);
        $auth->addChild($admin, $updatSalary);
        $auth->addChild($admin, $deleteSalary);
        $auth->addChild($admin, $indexSalary);
        $auth->addChild($admin, $createEmployee);
        $auth->addChild($admin, $updateEmployee);
        $auth->addChild($admin, $deleteEmployee);
        $auth->addChild($admin, $indexEmployee);
        $auth->addChild($admin, $userCanSeeStockLessThanZero);
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190127_093518_add_auth_itemes cannot be reverted.\n";

        return false;
    }
    */
}
