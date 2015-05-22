<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\widgets;


use gromver\modulequery\ModuleEvent;
use gromver\platform\basic\modules\main\widgets\events\DesktopEvent;
use Yii;
use yii\caching\Cache;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Desktop
 * Отображает панель управления (все компоненты системы)
 * Для сбора списка компонентов используется модульное событие \gromver\platform\basic\modules\main\widgets\events\DesktopEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Desktop extends \yii\bootstrap\Widget
{
    const EVENT_FETCH_ITEMS = 'DesktopItems';

    /**
     * @var string|Cache
     */
    public $cache = 'cache';
    /**
     * @var int
     */
    public $cacheDuration;
    /**
     * @var \yii\caching\Dependency
     */
    public $cacheDependency;
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
     *    - icon
     *    - template
     *    - options
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

    public $linkOptions = [];

    public $itemTemplate = '{header}{links}';

    public $headerTemplate = '<h4 class="text-muted">{icon}{label}</h4>';
    /**
     * @var string шаблон роутера, включает в себя {icon} и {link} - ссылка на компонент маршрутизатора либо непосредственно роут
     */
    public $linkTemplate = '{icon}{link}';
    /**
     * @var boolean whether the labels for menu items should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var string the CSS class to be appended to the active menu item.
     */
    public $activeCssClass = 'active';
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive()
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     * The activated parent menu items will also have its CSS classes appended with [[activeCssClass]].
     */
    public $activateLinks = true;
    //public $activateParents = false;
    /**
     * @var boolean whether to hide empty menu items. An empty menu item is one whose `url` option is not
     * set and which has no visible child menu items.
     */
    //public $hideEmptyItems = true;
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
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive()
     */
    public $route;
    /**
     * @var array the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive()
     */
    //public $params;
    /**
     * @var int
     */
    public $columns = 3;

    /**
     * Renders the menu.
     */
    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }

        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = [__CLASS__, $this->items];
            if (($this->items = $this->cache->get($cacheKey)) === false) {
                $this->items = ModuleEvent::trigger(self::EVENT_FETCH_ITEMS, new DesktopEvent([
                    'items' => $this->items
                ]), 'items');
                $this->cache->set($cacheKey, $this->items, $this->cacheDuration, $this->cacheDependency);
            }
        } else {
            $this->items = ModuleEvent::trigger(self::EVENT_FETCH_ITEMS, new DesktopEvent([
                'items' => $this->items
            ]), 'items');
        }

        $this->normalizeItems();
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        echo Html::tag($tag, $this->renderItems($this->columns), $options);
    }

    /**
     * @param null|integer $columns
     * @return string
     */
    protected function renderItems($columns = null)
    {
        $n = count($this->items);
        $lines = [];
        foreach ($this->items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'div');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
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
        $links = ''; $header = '';

        if (isset($item['label'])) {
            $template = ArrayHelper::getValue($item, 'headerTemplate', $this->headerTemplate);

            $header = strtr($template, [
                    '{icon}' => $item['icon'],
                    '{label}' => $item['label']
                ]);
        }

        foreach ($item['items'] as $link) {
            $template = ArrayHelper::getValue($link, 'template', $this->linkTemplate);

            $linkContent = strtr($template, [
                    '{icon}' => $link['icon'],
                    '{link}' => Html::a($link['label'], $link['url'])
                ]);

            $linkOptions = array_merge($this->linkOptions, $link['options']);

            $links .= Html::tag(ArrayHelper::remove($linkOptions, 'tag', 'div'), $linkContent, $linkOptions);
        }

        $template = ArrayHelper::getValue($item, 'template', $this->itemTemplate);

        return strtr($template, [
                '{header}' => $header,
                '{links}' => $links
            ]);

    }

    protected function normalizeItems()
    {
        foreach ($this->items as &$item) {
            $active = false;

            if (!isset($item['icon'])) {
                $item['icon'] = '';
            }

            foreach ($item['items'] as &$link) {
                if (!isset($link['url'])) {
                    $link['url'] = '#';
                }

                if (!isset($link['options'])) {
                    $link['options'] = [];
                }

                if (!isset($link['label'])) {
                    $link['label'] = '';
                }
                if ($this->encodeLabels) {
                    $link['label'] = Html::encode($link['label']);
                }

                if (!isset($link['icon'])) {
                    $link['icon'] = '';
                }

                if(!isset($link['active']))
                    if($this->activateLinks && $this->isLinkActive($link)) {
                        $active = $link['active'] = true;
                    } else {
                        $link['active'] = false;
                    }
            }

            if($this->activateItems) {
                $item['active'] = $active;
            }
        }
    }
        /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $link the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isLinkActive($link)
    {
        if (isset($link['url']) && is_array($link['url']) && isset($link['url'][0])) {
            $route = $link['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (strpos($this->route, ltrim($route, '/')) !== 0) {
                return false;
            }

            return true;
        }

        return false;
    }
}