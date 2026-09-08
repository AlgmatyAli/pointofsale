<?php

use yii\db\Migration;

/**
 * Class m260908_000000_add_auth_items
 */
class m260908_000000_add_auth_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Roles
        $admin = $auth->createRole('مدير');
        $admin->type = 1;
        $auth->add($admin);

        $helper = $auth->createRole('مساعد');
        $helper->type = 3;
        $auth->add($helper);

        // Permissions
        $barcodePrint = $auth->createPermission('barcodePrint');
        $barcodePrint->description = 'انشاء وطباعة الباركود';
        $auth->add($barcodePrint);

        $categoryHistrans = $auth->createPermission('categoryHistrans');
        $categoryHistrans->description = 'كشف حساب صنف';
        $auth->add($categoryHistrans);

        $changePassword = $auth->createPermission('changePassword');
        $changePassword->description = 'تعديل كلمة المرور';
        $auth->add($changePassword);

        $clientDepts = $auth->createPermission('clientDepts');
        $clientDepts->description = 'تقرير بإجمالي الديون';
        $auth->add($clientDepts);

        $clientHistrans = $auth->createPermission('clientHistrans');
        $clientHistrans->description = 'عرض كشف حساب عميل';
        $auth->add($clientHistrans);

        $companyInfo = $auth->createPermission('companyInfo');
        $companyInfo->description = 'ادارة بيانات المؤسسة';
        $auth->add($companyInfo);

        $createArrangment = $auth->createPermission('createArrangment');
        $createArrangment->description = 'انشاء تسوية جرد';
        $auth->add($createArrangment);

        $createBackSales = $auth->createPermission('createBackSales');
        $createBackSales->description = 'تسجيل مسترجع مبيعات';
        $auth->add($createBackSales);

        $createBranches = $auth->createPermission('createBranches');
        $createBranches->description = 'تسجيل فرع';
        $auth->add($createBranches);

        $createCategory = $auth->createPermission('createCategory');
        $createCategory->description = 'تسجيل الأصناف';
        $auth->add($createCategory);

        $createClient = $auth->createPermission('createClient');
        $createClient->description = 'تسجيل العملاء';
        $auth->add($createClient);

        $createEmployee = $auth->createPermission('createEmployee');
        $createEmployee->description = 'تسجيل بيانات الموظفين';
        $auth->add($createEmployee);

        $createExpenses = $auth->createPermission('createExpenses');
        $createExpenses->description = 'تسجيل المصروفات';
        $auth->add($createExpenses);

        $createItems = $auth->createPermission('createItems');
        $createItems->description = 'تسجيل بنود المصاريف';
        $auth->add($createItems);

        $createPurchases = $auth->createPermission('createPurchases');
        $createPurchases->description = 'تسجيل المشتريات';
        $auth->add($createPurchases);

        $createReciept = $auth->createPermission('createReciept');
        $createReciept->description = 'تسجيل ايصالات  ';
        $auth->add($createReciept);

        $createReorder = $auth->createPermission('createReorder');
        $createReorder->description = 'انشاء طلبية مشتريات';
        $auth->add($createReorder);

        $createSafe = $auth->createPermission('createSafe');
        $createSafe->description = 'تسجيل حركة الخزينة';
        $auth->add($createSafe);

        $createSalary = $auth->createPermission('createSalary');
        $createSalary->description = 'تسجيل المرتبات';
        $auth->add($createSalary);

        $createSales = $auth->createPermission('createSales');
        $createSales->description = 'تسجيل المبيعات';
        $auth->add($createSales);

        $createTransfer = $auth->createPermission('createTransfer');
        $createTransfer->description = 'تسجيل المقاصة';
        $auth->add($createTransfer);

        $createTransferItems = $auth->createPermission('createTransferItems');
        $createTransferItems->description = 'انشاء نقل الاصناف بين الفروع';
        $auth->add($createTransferItems);

        $createUsers = $auth->createPermission('createUsers');
        $createUsers->description = 'تسجيل مستخدم';
        $auth->add($createUsers);

        $deleteArrangment = $auth->createPermission('deleteArrangment');
        $deleteArrangment->description = 'الغاء تسوية جرد';
        $auth->add($deleteArrangment);

        $deleteBranches = $auth->createPermission('deleteBranches');
        $deleteBranches->description = 'حدف فرع';
        $auth->add($deleteBranches);

        $deleteCategory = $auth->createPermission('deleteCategory');
        $deleteCategory->description = 'حدف الأصناف';
        $auth->add($deleteCategory);

        $deleteClient = $auth->createPermission('deleteClient');
        $deleteClient->description = 'حذف عميل';
        $auth->add($deleteClient);

        $deleteEmployee = $auth->createPermission('deleteEmployee');
        $deleteEmployee->description = 'حذف بيانات الموظفين';
        $auth->add($deleteEmployee);

        $deleteExpenses = $auth->createPermission('deleteExpenses');
        $deleteExpenses->description = 'حذف المصروفات';
        $auth->add($deleteExpenses);

        $deleteItems = $auth->createPermission('deleteItems');
        $deleteItems->description = 'حدف بنود المصاريف';
        $auth->add($deleteItems);

        $deletePurchases = $auth->createPermission('deletePurchases');
        $deletePurchases->description = 'الغاء المشتريات';
        $auth->add($deletePurchases);

        $deleteReciept = $auth->createPermission('deleteReciept');
        $deleteReciept->description = 'حذف ايصالات ';
        $auth->add($deleteReciept);

        $deleteReorder = $auth->createPermission('deleteReorder');
        $deleteReorder->description = 'الغاء طلبية مشتريات';
        $auth->add($deleteReorder);

        $deleteSafe = $auth->createPermission('deleteSafe');
        $deleteSafe->description = 'حذف حركة خزينة';
        $auth->add($deleteSafe);

        $deleteSalary = $auth->createPermission('deleteSalary');
        $deleteSalary->description = 'حذف المرتبات';
        $auth->add($deleteSalary);

        $deleteSales = $auth->createPermission('deleteSales');
        $deleteSales->description = 'حذف المبيعات';
        $auth->add($deleteSales);

        $deleteTransfer = $auth->createPermission('deleteTransfer');
        $deleteTransfer->description = 'حذف المقاصة';
        $auth->add($deleteTransfer);

        $deleteTransferItems = $auth->createPermission('deleteTransferItems');
        $deleteTransferItems->description = 'الغاء نقل الاصناف بين الفروع';
        $auth->add($deleteTransferItems);

        $deleteUsers = $auth->createPermission('deleteUsers');
        $deleteUsers->description = 'حدف مستخدم';
        $auth->add($deleteUsers);

        $ftran = $auth->createPermission('ftran');
        $ftran->description = 'تقرير الحركة اليومية';
        $auth->add($ftran);

        $indexArrangment = $auth->createPermission('indexArrangment');
        $indexArrangment->description = 'تقرير تسوية جرد';
        $auth->add($indexArrangment);

        $indexBranches = $auth->createPermission('indexBranches');
        $indexBranches->description = 'شاشة الفروع';
        $auth->add($indexBranches);

        $indexCategory = $auth->createPermission('indexCategory');
        $indexCategory->description = 'شاشة الأصناف';
        $auth->add($indexCategory);

        $indexClient = $auth->createPermission('indexClient');
        $indexClient->description = 'تقرير العملاء';
        $auth->add($indexClient);

        $indexEmployee = $auth->createPermission('indexEmployee');
        $indexEmployee->description = 'شاشة الموظفين';
        $auth->add($indexEmployee);

        $indexExpenses = $auth->createPermission('indexExpenses');
        $indexExpenses->description = 'تقرير عن المصروفات';
        $auth->add($indexExpenses);

        $indexItems = $auth->createPermission('indexItems');
        $indexItems->description = 'شاشة بنود المصاريف';
        $auth->add($indexItems);

        $indexPurchases = $auth->createPermission('indexPurchases');
        $indexPurchases->description = 'تقرير عن المشتريات';
        $auth->add($indexPurchases);

        $indexReciept = $auth->createPermission('indexReciept');
        $indexReciept->description = 'تقرير عن ايصالات ';
        $auth->add($indexReciept);

        $indexReorder = $auth->createPermission('indexReorder');
        $indexReorder->description = 'تقرير طلبيات المشتريات';
        $auth->add($indexReorder);

        $indexSafe = $auth->createPermission('indexSafe');
        $indexSafe->description = 'تقرير حركة الخزينة';
        $auth->add($indexSafe);

        $indexSalary = $auth->createPermission('indexSalary');
        $indexSalary->description = 'شاشة المرتبات';
        $auth->add($indexSalary);

        $indexSales = $auth->createPermission('indexSales');
        $indexSales->description = 'تقرير عن المبيعات';
        $auth->add($indexSales);

        $indexTransfer = $auth->createPermission('indexTransfer');
        $indexTransfer->description = 'تقرير عن المقاصة';
        $auth->add($indexTransfer);

        $indexTransferItems = $auth->createPermission('indexTransferItems');
        $indexTransferItems->description = 'تقرير نقل الاصناف بين الفروع';
        $auth->add($indexTransferItems);

        $indexUsers = $auth->createPermission('indexUsers');
        $indexUsers->description = 'شاشة المستخدمين';
        $auth->add($indexUsers);

        $info = $auth->createPermission('info');
        $info->description = 'بطاعة معلومات صنف';
        $auth->add($info);

        $inventory = $auth->createPermission('inventory');
        $inventory->description = 'تقرير بالجرد';
        $auth->add($inventory);

        $moreRequest = $auth->createPermission('moreRequest');
        $moreRequest->description = 'تقرير بالأصناف الأكثر رواجا';
        $auth->add($moreRequest);

        $prices = $auth->createPermission('prices');
        $prices->description = 'قائمة الأسعـار';
        $auth->add($prices);

        $profit = $auth->createPermission('profit');
        $profit->description = 'تقرير عن الأرباح اليومية';
        $auth->add($profit);

        $reOrder = $auth->createPermission('reOrder');
        $reOrder->description = 'تقرير بالحد الأدنى للأصناف';
        $auth->add($reOrder);

        $SavePurchasesAsNew = $auth->createPermission('SavePurchasesAsNew');
        $SavePurchasesAsNew->description = 'نسخ فاتورة المشتريات';
        $auth->add($SavePurchasesAsNew);

        $saveSalesAsNew = $auth->createPermission('saveSalesAsNew');
        $saveSalesAsNew->description = 'نسخ فاتورة المبيعات';
        $auth->add($saveSalesAsNew);

        $updatClient = $auth->createPermission('updatClient');
        $updatClient->description = 'تعديل عميل';
        $auth->add($updatClient);

        $updateArrangment = $auth->createPermission('updateArrangment');
        $updateArrangment->description = 'تعديل تسوية جرد';
        $auth->add($updateArrangment);

        $updateBranches = $auth->createPermission('updateBranches');
        $updateBranches->description = 'تعديل فرع';
        $auth->add($updateBranches);

        $updateCategory = $auth->createPermission('updateCategory');
        $updateCategory->description = 'تعديل الأصناف';
        $auth->add($updateCategory);

        $updateExpenses = $auth->createPermission('updateExpenses');
        $updateExpenses->description = 'تعديل المصروفات';
        $auth->add($updateExpenses);

        $updateItems = $auth->createPermission('updateItems');
        $updateItems->description = 'تعديل بنود المصاريف';
        $auth->add($updateItems);

        $updatEmployee = $auth->createPermission('updatEmployee');
        $updatEmployee->description = 'تعديل بيانات الموظفين ';
        $auth->add($updatEmployee);

        $updatePurchases = $auth->createPermission('updatePurchases');
        $updatePurchases->description = 'تعديل المشتريات';
        $auth->add($updatePurchases);

        $updateReciept = $auth->createPermission('updateReciept');
        $updateReciept->description = 'تعديل ايصالات ';
        $auth->add($updateReciept);

        $updateReorder = $auth->createPermission('updateReorder');
        $updateReorder->description = 'تعديل طلبية المشتريات';
        $auth->add($updateReorder);

        $updateSafe = $auth->createPermission('updateSafe');
        $updateSafe->description = 'تعديل حركة خزينة';
        $auth->add($updateSafe);

        $updateSalary = $auth->createPermission('updateSalary');
        $updateSalary->description = 'تعديل المرتبات';
        $auth->add($updateSalary);

        $updateSales = $auth->createPermission('updateSales');
        $updateSales->description = 'تعديل المبيعات';
        $auth->add($updateSales);

        $updateTransfer = $auth->createPermission('updateTransfer');
        $updateTransfer->description = 'تعديل المقاصة';
        $auth->add($updateTransfer);

        $updateTransferItems = $auth->createPermission('updateTransferItems');
        $updateTransferItems->description = 'تعديل نقل الاصناف بين الفروع';
        $auth->add($updateTransferItems);

        $updateUsers = $auth->createPermission('updateUsers');
        $updateUsers->description = 'تعديل مستخدم';
        $auth->add($updateUsers);

        $upload = $auth->createPermission('upload');
        $upload->description = 'تحميل الأصناف من ملف إكسل';
        $auth->add($upload);

        $userCanSeeOtherUsersSales = $auth->createPermission('userCanSeeOtherUsersSales');
        $userCanSeeOtherUsersSales->description = 'امكانية مشاهدة مبيعات المستخدمين';
        $auth->add($userCanSeeOtherUsersSales);

        $userCanSeeStockLessThanZero = $auth->createPermission('userCanSeeStockLessThanZero');
        $userCanSeeStockLessThanZero->description = 'عرض الكميات الاقل من الصفر';
        $auth->add($userCanSeeStockLessThanZero);

        $userCansellOverDebt = $auth->createPermission('userCansellOverDebt');
        $userCansellOverDebt->description = 'امكانية البيع في حالة تجاوز سقف الدين';
        $auth->add($userCansellOverDebt);

        $userCanUpdateSalePriceAfterSave = $auth->createPermission('userCanUpdateSalePriceAfterSave');
        $userCanUpdateSalePriceAfterSave->description = 'امكانية تعديل سعر البيع بعد حفظ الفاتورة';
        $auth->add($userCanUpdateSalePriceAfterSave);

        // Assign all permissions to مدير
        $auth->addChild($admin, $barcodePrint);
        $auth->addChild($admin, $categoryHistrans);
        $auth->addChild($admin, $changePassword);
        $auth->addChild($admin, $clientDepts);
        $auth->addChild($admin, $clientHistrans);
        $auth->addChild($admin, $companyInfo);
        $auth->addChild($admin, $createArrangment);
        $auth->addChild($admin, $createBackSales);
        $auth->addChild($admin, $createBranches);
        $auth->addChild($admin, $createCategory);
        $auth->addChild($admin, $createClient);
        $auth->addChild($admin, $createEmployee);
        $auth->addChild($admin, $createExpenses);
        $auth->addChild($admin, $createItems);
        $auth->addChild($admin, $createPurchases);
        $auth->addChild($admin, $createReciept);
        $auth->addChild($admin, $createReorder);
        $auth->addChild($admin, $createSafe);
        $auth->addChild($admin, $createSalary);
        $auth->addChild($admin, $createSales);
        $auth->addChild($admin, $createTransfer);
        $auth->addChild($admin, $createTransferItems);
        $auth->addChild($admin, $createUsers);
        $auth->addChild($admin, $deleteArrangment);
        $auth->addChild($admin, $deleteBranches);
        $auth->addChild($admin, $deleteCategory);
        $auth->addChild($admin, $deleteClient);
        $auth->addChild($admin, $deleteEmployee);
        $auth->addChild($admin, $deleteExpenses);
        $auth->addChild($admin, $deleteItems);
        $auth->addChild($admin, $deletePurchases);
        $auth->addChild($admin, $deleteReciept);
        $auth->addChild($admin, $deleteReorder);
        $auth->addChild($admin, $deleteSafe);
        $auth->addChild($admin, $deleteSalary);
        $auth->addChild($admin, $deleteSales);
        $auth->addChild($admin, $deleteTransfer);
        $auth->addChild($admin, $deleteTransferItems);
        $auth->addChild($admin, $deleteUsers);
        $auth->addChild($admin, $ftran);
        $auth->addChild($admin, $indexArrangment);
        $auth->addChild($admin, $indexBranches);
        $auth->addChild($admin, $indexCategory);
        $auth->addChild($admin, $indexClient);
        $auth->addChild($admin, $indexEmployee);
        $auth->addChild($admin, $indexExpenses);
        $auth->addChild($admin, $indexItems);
        $auth->addChild($admin, $indexPurchases);
        $auth->addChild($admin, $indexReciept);
        $auth->addChild($admin, $indexReorder);
        $auth->addChild($admin, $indexSafe);
        $auth->addChild($admin, $indexSalary);
        $auth->addChild($admin, $indexSales);
        $auth->addChild($admin, $indexTransfer);
        $auth->addChild($admin, $indexTransferItems);
        $auth->addChild($admin, $indexUsers);
        $auth->addChild($admin, $info);
        $auth->addChild($admin, $inventory);
        $auth->addChild($admin, $moreRequest);
        $auth->addChild($admin, $prices);
        $auth->addChild($admin, $profit);
        $auth->addChild($admin, $reOrder);
        $auth->addChild($admin, $SavePurchasesAsNew);
        $auth->addChild($admin, $saveSalesAsNew);
        $auth->addChild($admin, $updatClient);
        $auth->addChild($admin, $updateArrangment);
        $auth->addChild($admin, $updateBranches);
        $auth->addChild($admin, $updateCategory);
        $auth->addChild($admin, $updateExpenses);
        $auth->addChild($admin, $updateItems);
        $auth->addChild($admin, $updatEmployee);
        $auth->addChild($admin, $updatePurchases);
        $auth->addChild($admin, $updateReciept);
        $auth->addChild($admin, $updateReorder);
        $auth->addChild($admin, $updateSafe);
        $auth->addChild($admin, $updateSalary);
        $auth->addChild($admin, $updateSales);
        $auth->addChild($admin, $updateTransfer);
        $auth->addChild($admin, $updateTransferItems);
        $auth->addChild($admin, $updateUsers);
        $auth->addChild($admin, $upload);
        $auth->addChild($admin, $userCanSeeOtherUsersSales);
        $auth->addChild($admin, $userCanSeeStockLessThanZero);
        $auth->addChild($admin, $userCansellOverDebt);
        $auth->addChild($admin, $userCanUpdateSalePriceAfterSave);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        // Remove permissions
        $userCanUpdateSalePriceAfterSave = $auth->getPermission('userCanUpdateSalePriceAfterSave');
        if ($userCanUpdateSalePriceAfterSave !== null) {
            $auth->remove($userCanUpdateSalePriceAfterSave);
        }

        $userCansellOverDebt = $auth->getPermission('userCansellOverDebt');
        if ($userCansellOverDebt !== null) {
            $auth->remove($userCansellOverDebt);
        }

        $userCanSeeStockLessThanZero = $auth->getPermission('userCanSeeStockLessThanZero');
        if ($userCanSeeStockLessThanZero !== null) {
            $auth->remove($userCanSeeStockLessThanZero);
        }

        $userCanSeeOtherUsersSales = $auth->getPermission('userCanSeeOtherUsersSales');
        if ($userCanSeeOtherUsersSales !== null) {
            $auth->remove($userCanSeeOtherUsersSales);
        }

        $upload = $auth->getPermission('upload');
        if ($upload !== null) {
            $auth->remove($upload);
        }

        $updateUsers = $auth->getPermission('updateUsers');
        if ($updateUsers !== null) {
            $auth->remove($updateUsers);
        }

        $updateTransferItems = $auth->getPermission('updateTransferItems');
        if ($updateTransferItems !== null) {
            $auth->remove($updateTransferItems);
        }

        $updateTransfer = $auth->getPermission('updateTransfer');
        if ($updateTransfer !== null) {
            $auth->remove($updateTransfer);
        }

        $updateSales = $auth->getPermission('updateSales');
        if ($updateSales !== null) {
            $auth->remove($updateSales);
        }

        $updateSalary = $auth->getPermission('updateSalary');
        if ($updateSalary !== null) {
            $auth->remove($updateSalary);
        }

        $updateSafe = $auth->getPermission('updateSafe');
        if ($updateSafe !== null) {
            $auth->remove($updateSafe);
        }

        $updateReorder = $auth->getPermission('updateReorder');
        if ($updateReorder !== null) {
            $auth->remove($updateReorder);
        }

        $updateReciept = $auth->getPermission('updateReciept');
        if ($updateReciept !== null) {
            $auth->remove($updateReciept);
        }

        $updatePurchases = $auth->getPermission('updatePurchases');
        if ($updatePurchases !== null) {
            $auth->remove($updatePurchases);
        }

        $updatEmployee = $auth->getPermission('updatEmployee');
        if ($updatEmployee !== null) {
            $auth->remove($updatEmployee);
        }

        $updateItems = $auth->getPermission('updateItems');
        if ($updateItems !== null) {
            $auth->remove($updateItems);
        }

        $updateExpenses = $auth->getPermission('updateExpenses');
        if ($updateExpenses !== null) {
            $auth->remove($updateExpenses);
        }

        $updateCategory = $auth->getPermission('updateCategory');
        if ($updateCategory !== null) {
            $auth->remove($updateCategory);
        }

        $updateBranches = $auth->getPermission('updateBranches');
        if ($updateBranches !== null) {
            $auth->remove($updateBranches);
        }

        $updateArrangment = $auth->getPermission('updateArrangment');
        if ($updateArrangment !== null) {
            $auth->remove($updateArrangment);
        }

        $updatClient = $auth->getPermission('updatClient');
        if ($updatClient !== null) {
            $auth->remove($updatClient);
        }

        $saveSalesAsNew = $auth->getPermission('saveSalesAsNew');
        if ($saveSalesAsNew !== null) {
            $auth->remove($saveSalesAsNew);
        }

        $SavePurchasesAsNew = $auth->getPermission('SavePurchasesAsNew');
        if ($SavePurchasesAsNew !== null) {
            $auth->remove($SavePurchasesAsNew);
        }

        $reOrder = $auth->getPermission('reOrder');
        if ($reOrder !== null) {
            $auth->remove($reOrder);
        }

        $profit = $auth->getPermission('profit');
        if ($profit !== null) {
            $auth->remove($profit);
        }

        $prices = $auth->getPermission('prices');
        if ($prices !== null) {
            $auth->remove($prices);
        }

        $moreRequest = $auth->getPermission('moreRequest');
        if ($moreRequest !== null) {
            $auth->remove($moreRequest);
        }

        $inventory = $auth->getPermission('inventory');
        if ($inventory !== null) {
            $auth->remove($inventory);
        }

        $info = $auth->getPermission('info');
        if ($info !== null) {
            $auth->remove($info);
        }

        $indexUsers = $auth->getPermission('indexUsers');
        if ($indexUsers !== null) {
            $auth->remove($indexUsers);
        }

        $indexTransferItems = $auth->getPermission('indexTransferItems');
        if ($indexTransferItems !== null) {
            $auth->remove($indexTransferItems);
        }

        $indexTransfer = $auth->getPermission('indexTransfer');
        if ($indexTransfer !== null) {
            $auth->remove($indexTransfer);
        }

        $indexSales = $auth->getPermission('indexSales');
        if ($indexSales !== null) {
            $auth->remove($indexSales);
        }

        $indexSalary = $auth->getPermission('indexSalary');
        if ($indexSalary !== null) {
            $auth->remove($indexSalary);
        }

        $indexSafe = $auth->getPermission('indexSafe');
        if ($indexSafe !== null) {
            $auth->remove($indexSafe);
        }

        $indexReorder = $auth->getPermission('indexReorder');
        if ($indexReorder !== null) {
            $auth->remove($indexReorder);
        }

        $indexReciept = $auth->getPermission('indexReciept');
        if ($indexReciept !== null) {
            $auth->remove($indexReciept);
        }

        $indexPurchases = $auth->getPermission('indexPurchases');
        if ($indexPurchases !== null) {
            $auth->remove($indexPurchases);
        }

        $indexItems = $auth->getPermission('indexItems');
        if ($indexItems !== null) {
            $auth->remove($indexItems);
        }

        $indexExpenses = $auth->getPermission('indexExpenses');
        if ($indexExpenses !== null) {
            $auth->remove($indexExpenses);
        }

        $indexEmployee = $auth->getPermission('indexEmployee');
        if ($indexEmployee !== null) {
            $auth->remove($indexEmployee);
        }

        $indexClient = $auth->getPermission('indexClient');
        if ($indexClient !== null) {
            $auth->remove($indexClient);
        }

        $indexCategory = $auth->getPermission('indexCategory');
        if ($indexCategory !== null) {
            $auth->remove($indexCategory);
        }

        $indexBranches = $auth->getPermission('indexBranches');
        if ($indexBranches !== null) {
            $auth->remove($indexBranches);
        }

        $indexArrangment = $auth->getPermission('indexArrangment');
        if ($indexArrangment !== null) {
            $auth->remove($indexArrangment);
        }

        $ftran = $auth->getPermission('ftran');
        if ($ftran !== null) {
            $auth->remove($ftran);
        }

        $deleteUsers = $auth->getPermission('deleteUsers');
        if ($deleteUsers !== null) {
            $auth->remove($deleteUsers);
        }

        $deleteTransferItems = $auth->getPermission('deleteTransferItems');
        if ($deleteTransferItems !== null) {
            $auth->remove($deleteTransferItems);
        }

        $deleteTransfer = $auth->getPermission('deleteTransfer');
        if ($deleteTransfer !== null) {
            $auth->remove($deleteTransfer);
        }

        $deleteSales = $auth->getPermission('deleteSales');
        if ($deleteSales !== null) {
            $auth->remove($deleteSales);
        }

        $deleteSalary = $auth->getPermission('deleteSalary');
        if ($deleteSalary !== null) {
            $auth->remove($deleteSalary);
        }

        $deleteSafe = $auth->getPermission('deleteSafe');
        if ($deleteSafe !== null) {
            $auth->remove($deleteSafe);
        }

        $deleteReorder = $auth->getPermission('deleteReorder');
        if ($deleteReorder !== null) {
            $auth->remove($deleteReorder);
        }

        $deleteReciept = $auth->getPermission('deleteReciept');
        if ($deleteReciept !== null) {
            $auth->remove($deleteReciept);
        }

        $deletePurchases = $auth->getPermission('deletePurchases');
        if ($deletePurchases !== null) {
            $auth->remove($deletePurchases);
        }

        $deleteItems = $auth->getPermission('deleteItems');
        if ($deleteItems !== null) {
            $auth->remove($deleteItems);
        }

        $deleteExpenses = $auth->getPermission('deleteExpenses');
        if ($deleteExpenses !== null) {
            $auth->remove($deleteExpenses);
        }

        $deleteEmployee = $auth->getPermission('deleteEmployee');
        if ($deleteEmployee !== null) {
            $auth->remove($deleteEmployee);
        }

        $deleteClient = $auth->getPermission('deleteClient');
        if ($deleteClient !== null) {
            $auth->remove($deleteClient);
        }

        $deleteCategory = $auth->getPermission('deleteCategory');
        if ($deleteCategory !== null) {
            $auth->remove($deleteCategory);
        }

        $deleteBranches = $auth->getPermission('deleteBranches');
        if ($deleteBranches !== null) {
            $auth->remove($deleteBranches);
        }

        $deleteArrangment = $auth->getPermission('deleteArrangment');
        if ($deleteArrangment !== null) {
            $auth->remove($deleteArrangment);
        }

        $createUsers = $auth->getPermission('createUsers');
        if ($createUsers !== null) {
            $auth->remove($createUsers);
        }

        $createTransferItems = $auth->getPermission('createTransferItems');
        if ($createTransferItems !== null) {
            $auth->remove($createTransferItems);
        }

        $createTransfer = $auth->getPermission('createTransfer');
        if ($createTransfer !== null) {
            $auth->remove($createTransfer);
        }

        $createSales = $auth->getPermission('createSales');
        if ($createSales !== null) {
            $auth->remove($createSales);
        }

        $createSalary = $auth->getPermission('createSalary');
        if ($createSalary !== null) {
            $auth->remove($createSalary);
        }

        $createSafe = $auth->getPermission('createSafe');
        if ($createSafe !== null) {
            $auth->remove($createSafe);
        }

        $createReorder = $auth->getPermission('createReorder');
        if ($createReorder !== null) {
            $auth->remove($createReorder);
        }

        $createReciept = $auth->getPermission('createReciept');
        if ($createReciept !== null) {
            $auth->remove($createReciept);
        }

        $createPurchases = $auth->getPermission('createPurchases');
        if ($createPurchases !== null) {
            $auth->remove($createPurchases);
        }

        $createItems = $auth->getPermission('createItems');
        if ($createItems !== null) {
            $auth->remove($createItems);
        }

        $createExpenses = $auth->getPermission('createExpenses');
        if ($createExpenses !== null) {
            $auth->remove($createExpenses);
        }

        $createEmployee = $auth->getPermission('createEmployee');
        if ($createEmployee !== null) {
            $auth->remove($createEmployee);
        }

        $createClient = $auth->getPermission('createClient');
        if ($createClient !== null) {
            $auth->remove($createClient);
        }

        $createCategory = $auth->getPermission('createCategory');
        if ($createCategory !== null) {
            $auth->remove($createCategory);
        }

        $createBranches = $auth->getPermission('createBranches');
        if ($createBranches !== null) {
            $auth->remove($createBranches);
        }

        $createBackSales = $auth->getPermission('createBackSales');
        if ($createBackSales !== null) {
            $auth->remove($createBackSales);
        }

        $createArrangment = $auth->getPermission('createArrangment');
        if ($createArrangment !== null) {
            $auth->remove($createArrangment);
        }

        $companyInfo = $auth->getPermission('companyInfo');
        if ($companyInfo !== null) {
            $auth->remove($companyInfo);
        }

        $clientHistrans = $auth->getPermission('clientHistrans');
        if ($clientHistrans !== null) {
            $auth->remove($clientHistrans);
        }

        $clientDepts = $auth->getPermission('clientDepts');
        if ($clientDepts !== null) {
            $auth->remove($clientDepts);
        }

        $changePassword = $auth->getPermission('changePassword');
        if ($changePassword !== null) {
            $auth->remove($changePassword);
        }

        $categoryHistrans = $auth->getPermission('categoryHistrans');
        if ($categoryHistrans !== null) {
            $auth->remove($categoryHistrans);
        }

        $barcodePrint = $auth->getPermission('barcodePrint');
        if ($barcodePrint !== null) {
            $auth->remove($barcodePrint);
        }

        // Remove roles
        $editItems = $auth->getRole('تعديل اصناف');
        if ($editItems !== null) {
            $auth->remove($editItems);
        }

        $otherShop = $auth->getRole('محل اخر');
        if ($otherShop !== null) {
            $auth->remove($otherShop);
        }

        $assistantManager = $auth->getRole('مساعد مدير');
        if ($assistantManager !== null) {
            $auth->remove($assistantManager);
        }

        $helper = $auth->getRole('مساعد');
        if ($helper !== null) {
            $auth->remove($helper);
        }

        $admin = $auth->getRole('مدير');
        if ($admin !== null) {
            $auth->remove($admin);
        }
    }
}
