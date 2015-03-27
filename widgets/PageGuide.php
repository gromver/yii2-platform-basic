<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\platform\basic\assets\CkeditorHighlightAsset;
use gromver\platform\basic\modules\main\models\DbState;
use gromver\platform\basic\modules\page\models\Page;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class PageGuide
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageGuide extends Widget
{
    use WidgetCacheTrait;

    /**
     * Page model or PageId or PageId:PageAlias
     * @var Page|string
     * @field modal
     * @url /grom/page/backend/default/select
     * @translation gromver.platform
     */
    public $rootPage;
    /**
     * Page model or PageId or PageId:PageAlias
     * @var Page|string
     * @field modal
     * @url /grom/page/backend/default/select
     * @translation gromver.platform
     */
    public $page;
    /**
     * @field list
     * @items layouts
     * @translation gromver.platform
     */
    public $layout = 'page/guideDefault';
    /**
     * @field yesno
     * @translation gromver.platform
     */
    public $showTranslations;
    /**
     * @field yesno
     * @translation gromver.platform
     */
    public $useHighlights = true;

    public function init()
    {
        if ($this->rootPage && !$this->rootPage instanceof Page) {
            $this->rootPage = Page::findOne(intval($this->rootPage));
        }

        if (empty($this->rootPage)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Page not found.'));
        }

        if ($this->page && !$this->page instanceof Page) {
            $this->page = Page::findOne(intval($this->page));
        }

        if (empty($this->page)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Page not found.'));
        }

        if ($this->useHighlights) {
            CkeditorHighlightAsset::register($this->getView());
        }

        $this->ensureCache();
    }

    protected function launch()
    {

        if (!$this->cache || (list($items, $prevModel, $nextModel) = Yii::$app->cache->get([__CLASS__, $this->rootPage->id, $this->page->id])) === false) {
            /** items list */
            if (($this->page->level - $this->rootPage->level) > 1) {
                $listRoot = $this->page->parents($this->page->isLeaf() ? 2 : 1)->one();
            } else {
                $listRoot = $this->rootPage;
            }
            /** @var $items \gromver\platform\basic\modules\page\models\Page[] */
            $items = $listRoot->children(2)->published()->all();
            if (count($items)) {
                $hasActiveChild = false;
                $items = $this->prepareItems($items, $items[0]->level, $hasActiveChild);
            }

            /** nav */
            $model = $this->page;
            $prevModel = $nextModel = null;
            // предыдущая страница
            if (!$this->rootPage->equals($this->page)) {
                if ($prevModel = $model->prev()->published()->one()) {
                    //пытаемся найти лист(правый)
                    if ($rgtLeaf = $prevModel->leaves()->published()->orderBy(['lft' => SORT_DESC])->one()) {
                        $prevModel = $rgtLeaf;
                    }
                } else {
                    $prevModel = $model->parent;
                }
            }
            // следущая страница
            if ($this->rootPage->equals($this->page)) {
                $nextModel = $model->children(1)->published()->one();
            } else {
                if (!($nextModel = $model->children(1)->published()->one() or $nextModel = $model->next()->published()->one())) {
                    //пытаемся выйти наверх
                    while(($parentModel = $model->parent) && !$parentModel->equals($this->rootPage)) {
                        if ($nextModel = $parentModel->next()->one()) break;
                        $model = $parentModel;
                    }
                }
            }

            Yii::$app->cache->set([__CLASS__, $this->rootPage->id, $this->page->id], [$items, $prevModel, $nextModel], $this->cacheDuration, ($this->cacheDependency ? $this->cacheDependency : DbState::dependency(Page::tableName())));
        }

        echo $this->render($this->layout, [
            'model' => $this->page,
            'rootModel' => $this->rootPage,
            'items' => $items,
            'prevModel' => $prevModel,
            'nextModel' => $nextModel
        ]);
    }

    /**
     * @param $rawItems Page[]
     * @param $level integer
     * @param $active bool
     * @return array
     */
    private function prepareItems(&$rawItems, $level, &$active)
    {
        $items = [];

        /** @var Page $model */
        while ($model = array_shift($rawItems)){
            if ($level == $model->level) {
                if ($isActive = $model->id == $this->page->id) {
                    $active = true;
                }
                $items[] = [
                    'id' => $model->id,
                    'label' => $model->title,
                    'url' => $model->getFrontendViewLink(),
                    'active' => $isActive,
                ];
            } elseif ($level < $model->level) {
                array_unshift($rawItems, $model);
                $hasActiveChild = false;
                $last = count($items) - 1;
                $items[$last]['items'] = $this->prepareItems($rawItems, $model->level, $hasActiveChild);
                $items[$last]['hasActiveChild'] = $hasActiveChild;
            } else {
                array_unshift($rawItems, $model);
                return $items;
            }
        }


        return $items;
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/grom/page/backend/default/update', 'id' => $this->page->id, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Update Page')]
            ],
            [
                'url' => ['/grom/page/backend/default/index'],
                'label' => '<i class="glyphicon glyphicon-th-list"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Pages list'), 'target' => '_blank']
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'page/guideDefault' => Yii::t('gromver.platform', 'Default'),
            'page/guideAdvanced' => Yii::t('gromver.platform', 'Advanced'),
        ];
    }
} 