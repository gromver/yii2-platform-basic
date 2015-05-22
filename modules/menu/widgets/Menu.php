<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\menu\widgets;


use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\platform\basic\modules\main\models\DbState;
use gromver\platform\basic\modules\widget\widgets\Widget;
use gromver\platform\basic\modules\widget\widgets\WidgetCacheTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Menu
 * Виджет отображает пункты меню.
 * Для рендеринга пунктов меню, используется виджет, конфигурация которого указана в своистве [[widgetConfig]]
 * по умолчанию используется 'yii\widgets\Menu', но можно указать любой другой виджет
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property array $items
 */
class Menu extends Widget
{
    use WidgetCacheTrait;

    /**
     * MenuTypeId or MenuTypeId:MenuTypeAlias
     * @var string
     * @field modal
     * @url /grom/menu/backend/type/select
     * @translation gromver.platform
     * @label Menu Type
     */
    public $type;
    /**
     * @field list
     * @items languages
     * @empty Autodetect
     * @translation gromver.platform
     */
    public $language;
    /**
     * @field yesno
     * @translation gromver.platform
     */
    public $showInaccessible = false;
    /**
     * @var boolean
     * @ignore
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     * @ignore
     */
    public $activateParents = true;
    /**
     * Конфигурация виджета используемого для отображения меню
     * @var array
     * @ignore
     */
    public $widgetConfig = [
        'class' => 'yii\widgets\Menu',
        'activeCssClass' => 'active',
        'firstItemCssClass' => 'first',
        'lastItemCssClass' => 'last',
        'activateItems' => false,           // эти настройки не имеют смысла, поэтому отключены, управлять активностью пунктов меню
        'activateParents' => false,         // следует через [[self::activateItems]] и [[self::activateParents]]
        'options' => ['class' => 'level-1']
    ];

    private $_items;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->type)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Menu type must be set.'));
        }

        $this->language or $this->language = Yii::$app->language;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        if (!isset($this->_items)) {
            if ($cache = $this->ensureCache()) {
                $cacheKey = [__CLASS__, $this->type, $this->activateItems, $this->activateParents, $this->language];
                if (($this->_items = $cache->get($cacheKey)) === false) {
                    $this->_items = $this->items();
                    $cache->set($cacheKey, $this->_items, $this->cacheDuration, $this->cacheDependency ? $this->cacheDependency : DbState::dependency(MenuItem::tableName()));
                }
            } else {
                $this->_items = $this->items();
            }
        }

        return $this->_items;
    }

    /**
     * @return array
     */
    protected function items()
    {
        $rawItems = MenuItem::find()->type($this->type)->published()->language($this->language)->orderBy('lft')->all();

        if (count($rawItems)) {
            return $this->prepareItems($rawItems, $rawItems[0]->level);
        }

        return [];
    }

    /**
     * Приводит выгрузку из бд к виду [[\yii\widgets\Menu::items]]
     * @param $rawItems array
     * @param $level integer
     * @return array
     */
    protected function prepareItems(&$rawItems, $level)
    {
        $items = [];
        $urlManager = Yii::$app->urlManager;

        /** @var MenuItem $model */
        while ($model = array_shift($rawItems)){
            if ($level == $model->level) {
                $linkParams = (array)Json::decode($model->link_params);
                $items[] = [
                    'id' => $model->id,
                    'label' => @$linkParams['title'] ? $linkParams['title'] : $model->title,
                    'url' => $model->link_type == MenuItem::LINK_ROUTE ? ($model->secure ? $urlManager->createAbsoluteUrl($model->getFrontendViewLink(), 'https') : $urlManager->createUrl($model->getFrontendViewLink())) : $model->link,
                    'access_rule' => $model->access_rule,
                    'submenuOptions' => [
                        'class' => 'level-' . $model->level
                    ],
                    'options' => [
                        'class' => @$linkParams['class'] ? $linkParams['class'] : null,
                        'target' => @$linkParams['target'] ? $linkParams['target'] : null,
                        'style' => @$linkParams['style'] ? $linkParams['style'] : null,
                        'rel' => @$linkParams['rel'] ? $linkParams['rel'] : null,
                        'onclick' => @$linkParams['onclick'] ? $linkParams['onclick'] : null,
                    ]
                ];
            } elseif ($level < $model->level) {
                array_unshift($rawItems, $model);
                $hasActiveChild = false;
                $last = count($items) - 1;
                $items[$last]['items'] = $this->prepareItems($rawItems, $model->level, $hasActiveChild);
            } else {
                array_unshift($rawItems, $model);
                return $items;
            }
        }

        return $items;
    }

    /**
     * Определяет активность, доступ и видимость пункта меню
     * @param $items array
     * @param $active boolean
     */
    protected function normalizeItems(&$items, &$active)
    {
        $activeMenuIds = Yii::$app->menuManager->getActiveMenuIds();

        foreach ($items as &$item) {
            if ($isActive = in_array($item['id'], $activeMenuIds)) {
                $active = true;
            }
            $item['active'] = $this->activateItems && $isActive;

            $item['accessible'] = empty($item['access_rule']) ? true : Yii::$app->user->can($item['access_rule']);
            $item['visible'] = $this->showInaccessible || $item['accessible'];
            if (isset($item['items'])) {
                $hasActiveChild = false;
                $this->normalizeItems($item['items'], $hasActiveChild);

                if (!$item['active']) {
                    $item['active'] = $this->activateParents && $hasActiveChild;
                }

                $item['hasActiveChild'] = $hasActiveChild;
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function launch()
    {
        $widgetClass = ArrayHelper::remove($this->widgetConfig, 'class', '\yii\widgets\Menu');
        $this->widgetConfig['items'] = $this->getItems();
        $this->normalizeItems($this->widgetConfig['items'], $hasActiveChild);

        echo $widgetClass::widget($this->widgetConfig);
    }

    /**
     * @inheritdoc
     */
    public function customControls()
    {
        return [
            [
                'url' => ['/grom/menu/backend/item/create', 'menuTypeId' => (int)$this->type, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-plus"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Create Menu Item')]
            ],
            [
                'url' => ['/grom/menu/backend/item/index', 'MenuItemSearch' => ['menu_type_id' => (int)$this->type, 'language' => $this->language]],
                'label' => '<i class="glyphicon glyphicon-th-list"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Menu Items list'), 'target' => '_blank']
            ],
        ];
    }

    /**
     * @return array
     */
    public static function languages()
    {
        return Yii::$app->getAcceptedLanguagesList();
    }
}