# ترقية واجهة منظومة المبيعات إلى AdminLTE 4 / Bootstrap 5 (RTL)

## الملفات

| الملف | مكانه في المشروع | الحالة |
|---|---|---|
| `views/layouts/main.php`, `header.php`, `left.php`, `content.php` | `views/layouts/` | يستبدل القديم |
| `views/layouts/main-login.php`, `saleLayout.php` | `views/layouts/` | يستبدل القديم |
| `widgets/SidebarMenu.php` | `widgets/` | جديد (بديل `dmstr\widgets\Menu`) |
| `config/sidebar-menu.php` | `config/` | جديد (عناصر القائمة) |
| `assets/AdminLteAsset.php`, `AppAsset.php` | `assets/` | جديد / يستبدل القديم |
| `web/css/bs5-compat.css` | `web/css/` | جديد (مؤقت) |

## 1) تثبيت ملفات AdminLTE 4 محلياً

من جذر المشروع (يحتاج Node.js على جهاز التطوير فقط):

```bash
npm i admin-lte@4 bootstrap-icons
mkdir -p web/vendor/adminlte
cp -r node_modules/admin-lte/dist/. web/vendor/adminlte/
cp -r node_modules/bootstrap-icons/font web/vendor/adminlte/bootstrap-icons
```

تحقق من وجود هذه الملفات (وعدّل المسارات في `AdminLteAsset.php` إن اختلفت الأسماء في إصدارك):
- `web/vendor/adminlte/css/adminlte.rtl.min.css`
- `web/vendor/adminlte/js/adminlte.min.js`
- `web/vendor/adminlte/bootstrap-icons/bootstrap-icons.min.css`

وتأكد أن `adminlte.rtl.min.css` يحتوي على أنماط Bootstrap (ابحث فيه عن `.btn-primary`).
إن لم يحتوها فاحذف السطر `'yii\bootstrap5\BootstrapAsset'` من الخطوة التالية.

## 2) الإعدادات

`config/params.php` (لودجتات Kartik):

```php
'bsVersion' => '5.x',
```

`config/web.php` داخل `components`: يمنع تحميل Bootstrap 3 من أي ودجت قديم ويمنع تكرار Bootstrap 5 CSS:

```php
'assetManager' => [
    'bundles' => [
        'yii\bootstrap\BootstrapAsset'       => ['css' => []],
        'yii\bootstrap\BootstrapPluginAsset' => ['js'  => []],
        'yii\bootstrap5\BootstrapAsset'      => ['css' => []],
    ],
],
```

وفي موديل `CompanyInfo` (لتحديث الكاش عند تغيير الاسم أو الـ skin):

```php
public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    Yii::$app->cache->delete('company-info');
}
```

## 3) أشياء يجب فحصها بعد التشغيل

1. **`ajax-modal-popup.js`**: Bootstrap 5 يسجّل `$('#modal').modal('show')` كإضافة jQuery طالما jQuery محمّل قبله، فالأرجح أن يعمل. لكن أي `data-toggle` أو `data-dismiss` أو `data-target` يجب أن يصبح `data-bs-*`.
2. **`css/style.css` و `util.css` و `main.css`**: على الأرجح من قالب تسجيل دخول جاهز وقد تغيّر `.container` و`.btn` عالمياً. عطّلها واحداً واحداً في `AppAsset` وقارن.
3. **صفحة الدخول** (`site/login.php`) وأي `SaleAsset`: لم تصلني ملفاتها، أرسلها لأرحّلها.
4. **القائمة الجانبية**: تظهر كل العناصر لكل المستخدمين كما في القديم. أضف `'visible'` لكل عنصر إن كانت عندك صلاحيات.
5. حقل `skin` في `CompanyInfo` لم يعد له معنى في AdminLTE 4. يُستخدم الآن فقط لاختيار شريط جانبي فاتح (`...-light`) أو داكن.

## 4) جدول الترحيل التدريجي للصفحات الداخلية (Bootstrap 3 → 5)

| Bootstrap 3 | Bootstrap 5 |
|---|---|
| `data-toggle`, `data-target`, `data-dismiss` | `data-bs-toggle`, `data-bs-target`, `data-bs-dismiss` |
| `col-xs-N` | `col-N` (باقي `col-sm/md/lg` كما هي) |
| `pull-left` / `pull-right` | `float-start` / `float-end` |
| `hidden-xs` / `visible-xs` | `d-none d-sm-block` / `d-sm-none` |
| `btn-default` | `btn-outline-secondary` أو `btn-light` |
| `btn-xs` | `btn-sm` |
| `label label-success` | `badge text-bg-success` |
| `panel`, `panel-heading`, `panel-body` | `card`, `card-header`, `card-body` |
| `well` | `card card-body` |
| `table-condensed` | `table-sm` |
| `img-responsive` | `img-fluid` |
| `input-group-addon` | `input-group-text` |
| `form-group` / `control-label` | `mb-3` / `form-label` |
| `text-left` / `text-right` | `text-start` / `text-end` |
| `sr-only` | `visually-hidden` |
| `glyphicon glyphicon-*` | `bi bi-*` |
| `help-block` | `invalid-feedback` |
| `yii\bootstrap\*` (Modal, ActiveForm, Alert...) | `yii\bootstrap5\*` |

بعد ترحيل كل الصفحات: احذف `bs5-compat.css` من `AppAsset`، ثم نظّف `composer.json` من
`yiisoft/yii2-bootstrap` و`yiisoft/yii2-bootstrap4` و`airani/yii2-adminlte-rtl` و`macgyer/yii2-materializecss`
(بعد التأكد أن لا حزمة أخرى تعتمد عليها: `composer why <package>`).
