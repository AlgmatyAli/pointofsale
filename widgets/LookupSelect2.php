<?php

namespace app\widgets;

use Yii;
use kartik\select2\Select2;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Select2 للعملاء أو الأصناف يبحث عبر Ajax (lookup/client أو lookup/category)
 * بدل تحميل الجدول كله داخل الصفحة. لا يُحمَّل إلا الاسم المختار حالياً.
 *
 * في نموذج:      $form->field($model, 'clinet')->widget(LookupSelect2::class, ['lookup' => 'client', 'types' => [0, 2]])
 * في فلتر جدول:  'filterType' => LookupSelect2::class, 'filter' => [],
 *                'filterWidgetOptions' => ['lookup' => 'category']
 */
class LookupSelect2 extends Select2
{
    /** @var string client | category | company */
    public $lookup = 'client';

    /** @var int[]|null أنواع العملاء (client.type) المسموح بها، مثل [0, 2] للعملاء و [1, 2] للموردين */
    public $types;

    public function init()
    {
        $value = $this->hasModel()
            ? Html::getAttributeValue($this->model, $this->attribute)
            : $this->value;

        // نضيف للقائمة الاسم المختار فقط حتى يظهر عند فتح الصفحة
        $this->data = ($this->data ?: []) + self::names($this->lookup, $value);

        $params = ['/lookup/' . $this->lookup];
        if ($this->types !== null) {
            $params['types'] = implode(',', array_map('intval', (array) $this->types));
        }

        $this->pluginOptions = ArrayHelper::merge([
            'minimumInputLength' => 0,
            'ajax' => [
                'url' => Url::to($params),
                'dataType' => 'json',
                'delay' => 300,
                'cache' => true,
                'data' => new JsExpression('function (params) { return {q: params.term || "", page: params.page || 1}; }'),
            ],
        ], $this->pluginOptions ?: []);

        parent::init();
    }

    /** الأسماء للقيم المختارة حالياً: [id => name] */
    public static function names(string $lookup, $value): array
    {
        if (is_string($value) && strpos($value, ',') !== false) {
            $value = explode(',', $value);
        }
        $ids = array_values(array_filter(array_map('intval', (array) $value)));
        if (!$ids) {
            return [];
        }
        $table = ['category' => 'category', 'company' => 'itemCompany'][$lookup] ?? 'client';
        return (new Query())->select(['name', 'id'])->from($table)
            ->where(['id' => $ids])->indexBy('id')->column();
    }
}
