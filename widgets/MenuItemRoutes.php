<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\widgets\ModalIFrame;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class MenuItemRoutes
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuItemRoutes extends \yii\bootstrap\Widget
{
    /**
     * @var array list of menu items. Each menu item should be an array of the following structure:
     *
     * - label: string, optional, specifies the menu item label. When [[encodeLabels]] is true, the label
     *   will be HTML-encoded. If the label is not specified, an empty string will be used.
     * - url: string or array, optional, specifies the URL of the menu item. It will be processed by [[Url::to]].
     *   When this is set, the actual menu item content will be generated using [[linkTemplate]];
     *   otherwise, [[labelTemplate]] will be used.
     * - visible: boolean, optional, whether this menu item is visible. Defaults to true.
     * - items: array, optional, specifies the sub-menu items. Its format is the same as the parent items.
     * - active: boolean, optional, whether this menu item is in active state (currently selected).
     *   If a menu item is active, its CSS class will be appended with [[activeCssClass]].
     *   If this option is not set, the menu item will be set active automatically when the current request
     *   is triggered by `url`. For more details, please refer to [[isItemActive()]].
     * - template: string, optional, the template used to render the content of this menu item.
     *   The token `{url}` will be replaced by the URL associated with this menu item,
     *   and the token `{label}` will be replaced by the label of the menu item.
     *   If this option is not set, [[linkTemplate]] or [[labelTemplate]] will be used instead.
     * - options: array, optional, the HTML attributes for the menu container tag.
     *
     * - label
     * - icon
     * - options
     * - template - itemTemplate
     * - headerTemplate
     * - items:
     *    - label
     *    - url
     *    - route
     *    - icon
     *    - options
     *    - template
     * - access todo
     * - visible todo
     */
    public $items = [];
    /**
     * @var array list of HTML attributes for the menu container tag. This will be overwritten
     * by the "options" set in individual [[items]]. The following special options are recognized:
     *
     * - tag: string, defaults to "li", the tag name of the item container tags.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $itemOptions = [];

    public $routerOptions = [];

    public $itemTemplate = '{header}{routers}';

    public $headerTemplate = '<h4 class="text-muted">{icon}{label}</h4>';
    /**
     * @var string шаблон роутера, включает в себя {icon} и {link} - ссылка на компонент маршрутизатора либо непосредственно роут
     */
    public $routerTemplate = '{icon}{link}';
    /**
     * @var boolean whether the labels for menu items should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var array the HTML attributes for the menu's container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "ul", the tag name of the item container tags.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [
        'class' => 'clearfix'
    ];
    /**
     * @var string the CSS class that will be assigned to the first item in the main menu or each submenu.
     * Defaults to null, meaning no such CSS class will be assigned.
     */
    public $firstItemCssClass;
    /**
     * @var string the CSS class that will be assigned to the last item in the main menu or each submenu.
     * Defaults to null, meaning no such CSS class will be assigned.
     */
    public $lastItemCssClass;
    /**
     * @var int
     */
    public $columns = 3;

    /**
     * Renders the menu.
     */
    public function run()
    {
        $this->normalizeItems();
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        echo Html::tag($tag, $this->renderItems($this->columns), $options);
    }

    /**
     * @param int $columns
     * @return string the rendering result
     */
    protected function renderItems($columns = null)
    {
        $n = count($this->items);
        $lines = [];
        foreach ($this->items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            $content = $this->renderItem($item);

            $lines[] = Html::tag($tag, $content, $options);
        }
        if(in_array($columns, [2,3,4])) {
            $colClass = 'col-sm-'.(12/$columns);
            $output = '';
            $lineCount = count($lines); $offset = 0;
            while ($lineCount) {
                $length = ceil($lineCount/$columns);
                $output .= Html::tag('div', implode("\n", array_slice($lines, $offset, $length)), ['class' => $colClass]);
                $offset += $length;
                $lineCount -= $length;
                $columns--;
            }

            return $output;
        }

        return implode("\n", $lines);
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item)
    {
        $routers = ''; $header = '';

        if (isset($item['label'])) {
            $template = ArrayHelper::getValue($item, 'headerTemplate', $this->headerTemplate);

            $header = strtr($template, [
                '{icon}' => $item['icon'],
                '{label}' => $item['label']
            ]);
        }

        foreach ($item['items'] as $router) {
            $template = ArrayHelper::getValue($router, 'template', $this->routerTemplate);

            $routerContent = strtr($template, [
                '{icon}' => $router['icon'],
                '{link}' => isset($router['url']) ? Html::a($router['label'], $router['url']) : Html::a($router['label'], '#', [
                    'onclick' => ModalIFrame::postDataJs([
                        'route' => MenuItem::toRoute($router['route']),
                        'link' => Yii::$app->urlManager->createUrl($router['route'])
                    ])
                ])
            ]);

            $routerOptions = array_merge($this->routerOptions, $router['options']);

            $routers .= Html::tag(ArrayHelper::remove($routerOptions, 'tag', 'div'), $routerContent, $routerOptions);
        }

        $template = ArrayHelper::getValue($item, 'template', $this->itemTemplate);

        return strtr($template, [
            '{header}' => $header,
            '{routers}' => $routers
        ]);
    }

    /**
     * @return array the normalized menu items
     */
    protected function normalizeItems()
    {
        foreach ($this->items as &$item) {
            if (!isset($item['icon'])) {
                $item['icon'] = '';
            }

            foreach ($item['items'] as &$route) {
                if (!isset($route['options'])) {
                    $route['options'] = [];
                }

                if (!isset($route['label'])) {
                    $route['label'] = '';
                } elseif ($this->encodeLabels) {
                    $route['label'] = Html::encode($route['label']);
                }

                if (!isset($route['icon'])) {
                    $route['icon'] = '';
                }

                if (!isset($route['url']) && !isset($route['route'])) {
                    $route['route'] = '';
                }
            }
        }
    }
}