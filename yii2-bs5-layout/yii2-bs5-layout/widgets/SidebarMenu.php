<?php

namespace app\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

/**
 * بديل dmstr\widgets\Menu: يبني قائمة AdminLTE 4 (treeview) فوق yii\widgets\Menu،
 * فتبقى كل مزايا Yii كما هي: url كمصفوفة، visible، تحديد العنصر النشط تلقائياً.
 *
 * مثال عنصر: ['label' => 'المبيعات', 'icon' => 'bi-cart3', 'url' => ['/sale/create']]
 * العناصر الفرعية تأخذ الأيقونة bi-circle افتراضياً.
 */
class SidebarMenu extends Menu
{
    public $options = [
        'class' => 'nav sidebar-menu flex-column',
        'data-lte-toggle' => 'treeview',
        'role' => 'menu',
        'data-accordion' => 'false',
    ];

    public $itemOptions = ['class' => 'nav-item'];

    public $submenuTemplate = "\n<ul class=\"nav nav-treeview\">\n{items}\n</ul>\n";

    public $activateParents = true;

    // AdminLTE يفتح القائمة الفرعية بالصنف menu-open على عنصر li
    public $activeCssClass = 'menu-open';

    // التشفير يتم يدوياً داخل renderItem
    public $encodeLabels = false;

    public $defaultIcon = 'bi-circle';

    protected function renderItem($item)
    {
        $hasChildren = !empty($item['items']);
        $icon = $item['icon'] ?? $this->defaultIcon;

        $label = Html::encode($item['label']);
        if ($hasChildren) {
            $label .= '<i class="nav-arrow bi bi-chevron-left"></i>';
        }

        $url = ($hasChildren || !isset($item['url'])) ? '#' : Url::to($item['url']);

        return Html::a(
            '<i class="nav-icon bi ' . Html::encode($icon) . '"></i><p>' . $label . '</p>',
            $url,
            ['class' => 'nav-link' . (!empty($item['active']) ? ' active' : '')]
        );
    }
}
