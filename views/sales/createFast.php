<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\TempInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\TempInvoice */

use fedemotta\datatables\DataTables;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

$isAjax = Yii::$app->request->isAjax;
$seeCostPrice = (Yii::$app->user->identity->seeCostPrice == 1);

$this->registerCss(<<<'CSS'
.fast-add .sx-qty { width: 5.5rem; text-align: center; font-weight: 700; }
.fast-add .sx-qty:focus { border-color: var(--bs-success); box-shadow: 0 0 0 .2rem rgba(var(--bs-success-rgb), .25); }
.fast-add tr.sx-picked > td { background-color: var(--bs-success-bg-subtle) !important; }
.fast-add .fast-bar { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .6rem 0; margin-top: .75rem; }
.fast-add .dataTables_filter input, .fast-add .dt-search input { min-width: 14rem; }
.fast-add table.dataTable td, .fast-add table.dataTable th { vertical-align: middle; }
CSS);
?>
<div class="temp-invoice-index fast-add">

    <?php $form = ActiveForm::begin(); ?>

    <!-- الرأس + تلميح الاستخدام -->
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-lightning-charge-fill text-info fs-4"></i>
        <div>
            <div class="fw-bold"><?= Html::encode($this->title ?: Yii::t('app', 'Adedd Fast')) ?></div>
            <div class="small text-body-secondary">اكتب الكمية المطلوبة بجانب كل صنف ثم اضغط «إضافة»؛ يُحدَّد الصنف تلقائياً عند كتابة الكمية.</div>
        </div>
    </div>

    <?= $form->field($model, 'id', ['template' => '{input}', 'options' => ['class' => 'd-none']])->hiddenInput() ?>

    <div class="table-responsive">
        <?= DataTables::widget([
            'summary' => '',
            'formatter' => Yii::$app->formatter,
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-sm table-hover table-striped align-middle w-100'],
            'clientOptions' => [
                'deferRender' => true,
                'responsive' => true,
                'autoWidth' => true,
                'lengthMenu' => [[50, 100, 250, 500, -1], [50, 100, 250, 500, 'All']],
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'ابحث بالاسم أو الرقم ...',
                ],
            ],
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],

                [
                    'label' => Yii::t('app', 'Serial'),
                    'value' => fn($data) => $data->id,
                ],

                [
                    'label' => Yii::t('app', 'Serial No'),
                    'contentOptions' => ['class' => 'text-danger'],
                    'value' => fn($data) => $data->serialNo,
                ],

                [
                    'attribute' => 'category',
                    'label' => Yii::t('app', 'Category'),
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width:45%'],
                    // سعر التكلفة يظهر كتلميح فقط لمن يملك صلاحية seeCostPrice (كان الكود القديم يعرض السعر الأدنى بعنوان "Cost Price" لغيرهم)
                    'contentOptions' => function ($model) use ($seeCostPrice) {
                        $options = ['class' => 'fw-semibold'];
                        if ($seeCostPrice) {
                            $options['data-bs-toggle'] = 'tooltip';
                            $options['data-bs-placement'] = 'top';
                            $options['data-bs-container'] = 'body';
                            $options['title'] = Yii::t('app', 'Cost Price') . ' ' . $model->costPrice;
                        }
                        return $options;
                    },
                    'value' => fn($data) => Html::encode((string) $data->name),
                ],

                [
                    'label' => Yii::t('app', 'Company'),
                    'value' => fn($data) => $data->company,
                ],

                [
                    'attribute' => 'quantity',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $q = (float) $data->quantity;
                        $cls = $q > 0 ? 'text-bg-success' : 'text-bg-secondary';
                        return '<span class="badge ' . $cls . '">' . Html::encode((string) $data->quantity) . '</span>';
                    },
                ],

                [
                    'attribute' => 'textInputValues',
                    'label' => 'كمية مطلوبة',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::textInput('TempInvoice[textInputValues][' . $model->id . ']', null, [
                            'class' => 'form-control form-control-sm sx-qty',
                            'maxlength' => 10,
                            'inputmode' => 'decimal',
                            'autocomplete' => 'off',
                            'placeholder' => '0',
                        ]);
                    },
                ],

                [
                    'label' => Yii::t('app', 'Max Price'),
                    'contentOptions' => ['class' => 'text-danger fw-semibold'],
                    'value' => fn($data) => $data->maxPrice,
                ],
                [
                    'label' => Yii::t('app', 'Min Price'),
                    'contentOptions' => ['class' => 'text-body-secondary'],
                    'value' => fn($data) => $data->minPrice,
                ],
            ],
        ]) ?>
    </div>

    <!-- شريط الإجراءات الثابت أسفل النافذة -->
    <div class="fast-bar d-flex align-items-center gap-2">
        <?= Html::submitButton(
            '<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Add') . ' <span class="badge text-bg-light ms-1" id="fast-count">0</span>',
            ['class' => 'btn btn-success btn-lg flex-grow-1', 'id' => 'fast-submit']
        ) ?>
        <?php if ($isAjax): ?>
            <button type="button" class="btn btn-lg btn-outline-secondary" data-bs-dismiss="modal">
                <?= Html::encode(Yii::t('app', 'Cancel')) ?>
            </button>
        <?php else: ?>
            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    var $root = $('.fast-add');

    function refreshCount() {
        var n = $root.find('input[type=checkbox][name="selection[]"]:checked').length;
        $('#fast-count').text(n);
    }

    // كتابة كمية => تحديد الصنف تلقائياً؛ مسحها => إلغاء التحديد
    $root.on('input', '.sx-qty', function () {
        var $tr = $(this).closest('tr');
        var has = $.trim(this.value) !== '' && parseFloat(this.value) > 0;
        $tr.find('input[type=checkbox][name="selection[]"]').prop('checked', has);
        $tr.toggleClass('sx-picked', has);
        refreshCount();
    });
    $root.on('change', 'input[type=checkbox]', function () {
        $(this).closest('tr').toggleClass('sx-picked', this.checked);
        refreshCount();
    });

    // Enter داخل مربع البحث لا يجب أن يرسل النموذج
    $root.on('keydown', '.dataTables_filter input, .dt-search input', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); }
    });

    // DataTables لا تُبقي في الصفحة إلا صفوف الصفحة الحالية، فتضيع الاختيارات التي تمت في صفحات أخرى أو قبل التصفية.
    // قبل الإرسال نُلحق اختيارات الصفوف غير الظاهرة كحقول مخفية.
    $root.closest('form').add($root.find('form')).on('submit', function () {
        var form = this;
        try {
            $.fn.dataTable.tables({api: true}).rows().every(function () {
                var node = this.node();
                if (!node || $.contains(document, node)) { return; }
                $(node).find('input[name]').each(function () {
                    var isCheck = this.type === 'checkbox';
                    if ((isCheck && this.checked) || (!isCheck && $.trim(this.value) !== '')) {
                        $('<input type="hidden">').attr('name', this.name).val(this.value).appendTo(form);
                    }
                });
            });
        } catch (err) { /* لا نمنع الإرسال عند أي خطأ */ }
    });

    // تلميحات Bootstrap 5 (تفويض أحداث: تعمل على الصفوف المرسومة لاحقاً)
    if (window.bootstrap && !window.__fastTooltips) {
        window.__fastTooltips = true;
        new bootstrap.Tooltip(document.body, {selector: '[data-bs-toggle="tooltip"]'});
    }

    // تركيز على مربع البحث بعد جاهزية الجدول/النافذة
    setTimeout(function () {
        $root.find('.dataTables_filter input, .dt-search input').first().trigger('focus');
    }, 350);
})();
JS, View::POS_READY, 'fast-add-js');
