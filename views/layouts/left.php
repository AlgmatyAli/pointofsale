<?php

use yii\helpers\Html;
use app\models\User;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<aside class="main-sidebar">

    <?= dmstr\widgets\Menu::widget(
        [
            'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
            'items' => [
                ['label' => 'تسجيل المبيعات', 'icon' => 'shopping-cart', 'url' => ['/temp-invoice/create', 'type' => '1'],],
                [
                    'label' => 'ترجيع المبيعات',
                    'icon' => 'calculator',
                    'url' => '#',
                    'items' => [
                        ['label' => 'تسجيل مسترجع المبيعات', 'icon' => 'shopping-cart', 'url' => ['/temp-back-sales/create'],],
                        ['label' => 'تسجيل مسترجع المبيعات حسب الزبون', 'icon' => 'shopping-cart', 'url' => ['/temp-back-sales/create-by-client'],],
                    ],
                ],
                ['label' => 'تسجيل المشتريات', 'icon' => 'cart-plus', 'url' => ['/temp-invoice-purchase/create'],],
                ['label' => Yii::t('app', "Reports"), 'icon' => 'tv', 'url' => ['/site/report'],],
                ['label' => 'تسجيل طلبية مشتريات', 'icon' => 'cart-plus', 'url' => ['/temp-reorder/create'],],
                ['label' => 'ايصالات القبض', 'icon' => 'file-text-o', 'url' => ['/receipt/create', 'type' => '1'],],
                ['label' => 'تسجيل الأصناف والمواد', 'icon' => 'plus-square', 'url' => ['/category'],],
                [
                    'label' => 'الأجور والمرتبات',
                    'icon' => 'calculator',
                    'url' => '#',
                    'items' => [
                        ['label' => 'تسجيل الموظفين', 'icon' => 'plus-square', 'url' => ['/employee/index'],],
                        ['label' => 'تسجيل حركة السحب', 'icon' => 'eye', 'url' => ['/emp-salary/create'],],
                        ['label' => 'تسجيل الاضافي والمكافئات ', 'icon' => 'eye', 'url' => ['/emp-salary/c-extra-job'],],
                        ['label' => 'تسجيل الخصومات', 'icon' => 'eye', 'url' => ['/emp-salary/c-discount'],],
                        ['label' => 'تسجيل الســلف', 'icon' => 'eye', 'url' => ['/loans/create'],],
                        ['label' => 'تسجيل خصم السلف', 'icon' => 'eye', 'url' => ['/loan-paid/create'],],
                        ['label' => 'تقرير السلف', 'icon' => 'eye', 'url' => ['/loans/index'],],
                        ['label' => 'كشف حساب سلفة', 'icon' => 'eye', 'url' => ['/loan-paid/index'],],
                        ['label' => 'التقرير العــام', 'icon' => 'eye', 'url' => ['/emp-salary/index'],],
                    ],
                ],
                ['label' => 'ايصالات الصرف', 'icon' => 'file-text-o', 'url' => ['/receipt/create', 'type' => '2'],],
                [
                    'label' => 'عمليات اخرى',
                    'icon' => 'calculator',
                    'url' => '#',
                    'items' => [
                        ['label' => 'تسجيل خصم على الزبائن', 'icon' => 'shopping-cart', 'url' => ['/disscount-clients/create-clients', 'type' => '1'],],
                        ['label' => 'تسجيل خصم على الموردين', 'icon' => 'shopping-cart', 'url' => ['/disscount-clients/create-suppliers', 'type' => '2'],],
                    ],
                ],
                ['label' => 'تسجيل العملاء', 'icon' => 'address-card', 'url' => ['/client/create'],],
                ['label' => Yii::t('app', "Dashboard"), 'icon' => 'windows', 'url' => ['/site/dashboard'],],
                ['label' => 'نقل الأصناف بين الفروع', 'icon' => 'plane', 'url' => ['/temp-transfer-items/create'],],
                ['label' => 'تسوية رصيد الجرد', 'icon' => 'plane', 'url' => ['/temp-arrangement/create'],],
                ['label' => 'تسجيل المصروفات', 'icon' => 'eye', 'url' => ['/expenses/create'],],
                ['label' => 'حركة الخزينة الرئيسية', 'icon' => 'car', 'url' => ['/safe/create'],],
                ['label' => 'حركة نقل النقدية بين الفروع', 'icon' => 'bus', 'url' => ['/transfer/create'],],

            ],
        ]
    ) ?>

    </section>

</aside>