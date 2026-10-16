<?php

/**
 * عناصر الشريط الجانبي. للتحكم بالصلاحيات أضف لأي عنصر:
 *   'visible' => Yii::$app->user->can('permissionName'),
 * الأيقونات من Bootstrap Icons: https://icons.getbootstrap.com
 */
return [
    ['label' => 'تسجيل المبيعات', 'icon' => 'bi-cart3', 'url' => ['/temp-invoice/create', 'type' => '1']],
    [
        'label' => 'ترجيع المبيعات',
        'icon' => 'bi-arrow-return-left',
        'url' => '#',
        'items' => [
            ['label' => 'تسجيل مسترجع المبيعات', 'url' => ['/temp-back-sales/create']],
            ['label' => 'تسجيل مسترجع المبيعات حسب الزبون', 'url' => ['/temp-back-sales/create-by-client']],
        ],
    ],
    ['label' => 'تسجيل المشتريات', 'icon' => 'bi-cart-plus', 'url' => ['/temp-invoice-purchase/create']],
    ['label' => Yii::t('app', 'Reports'), 'icon' => 'bi-bar-chart-line', 'url' => ['/site/report']],
    ['label' => 'تسجيل طلبية مشتريات', 'icon' => 'bi-clipboard-plus', 'url' => ['/temp-reorder/create']],
    ['label' => 'ايصالات القبض', 'icon' => 'bi-receipt', 'url' => ['/receipt/create', 'type' => '1']],
    ['label' => 'تسجيل الأصناف والمواد', 'icon' => 'bi-boxes', 'url' => ['/category']],
    [
        'label' => 'الأجور والمرتبات',
        'icon' => 'bi-people',
        'url' => '#',
        'items' => [
            ['label' => 'تسجيل الموظفين', 'url' => ['/employee/index']],
            ['label' => 'تسجيل حركة السحب', 'url' => ['/emp-salary/create']],
            ['label' => 'تسجيل الاضافي والمكافئات', 'url' => ['/emp-salary/c-extra-job']],
            ['label' => 'تسجيل الخصومات', 'url' => ['/emp-salary/c-discount']],
            ['label' => 'تسجيل الســلف', 'url' => ['/loans/create']],
            ['label' => 'تسجيل خصم السلف', 'url' => ['/loan-paid/create']],
            ['label' => 'تقرير السلف', 'url' => ['/loans/index']],
            ['label' => 'كشف حساب سلفة', 'url' => ['/loan-paid/index']],
            ['label' => 'التقرير العــام', 'url' => ['/emp-salary/index']],
        ],
    ],
    ['label' => 'ايصالات الصرف', 'icon' => 'bi-file-earmark-text', 'url' => ['/receipt/create', 'type' => '2']],
    [
        'label' => 'عمليات اخرى',
        'icon' => 'bi-calculator',
        'url' => '#',
        'items' => [
            ['label' => 'تسجيل خصم على الزبائن', 'url' => ['/disscount-clients/create-clients', 'type' => '1']],
            ['label' => 'تسجيل خصم على الموردين', 'url' => ['/disscount-clients/create-suppliers', 'type' => '2']],
        ],
    ],
    ['label' => 'تسجيل العملاء', 'icon' => 'bi-person-vcard', 'url' => ['/client/create']],
    ['label' => Yii::t('app', 'Dashboard'), 'icon' => 'bi-speedometer2', 'url' => ['/site/dashboard']],
    ['label' => 'نقل الأصناف بين الفروع', 'icon' => 'bi-arrow-left-right', 'url' => ['/temp-transfer-items/create']],
    ['label' => 'تسوية رصيد الجرد', 'icon' => 'bi-clipboard-check', 'url' => ['/temp-arrangement/create']],
    ['label' => 'تسجيل المصروفات', 'icon' => 'bi-cash-coin', 'url' => ['/expenses/create']],
    ['label' => 'حركة الخزينة الرئيسية', 'icon' => 'bi-safe2', 'url' => ['/safe/create']],
    ['label' => 'حركة نقل النقدية بين الفروع', 'icon' => 'bi-bank', 'url' => ['/transfer/create']],
];
