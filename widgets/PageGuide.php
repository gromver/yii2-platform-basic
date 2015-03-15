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
    /**
     * Page model or PageId or PageId:PageAlias
     * @var Page|string
     * @type modal
     * @url /grom/page/backend/default/select
     * @translation gromver.platform
     */
    public $rootPage;
    /**
     * Page model or PageId or PageId:PageAlias
     * @var Page|string
     * @type modal
     * @url /grom/page/backend/default/select
     * @translation gromver.platform
     */
    public $page;
    /**
     * @type list
     * @items layouts
     * @translation gromver.platform
     */
    public $layout = 'page/guide';
    /**
     * @type yesno
     * @translation gromver.platform
     */
    public $showTranslations;
    /**
     * @type yesno
     * @translation gromver.platform
     */
    public $useHighlights = true;

    protected function launch()
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

        /** items list */
        /** @var $items \gromver\platform\basic\modules\page\models\Page[] */
        if ($this->rootPage->equals($this->page)) {
            $items = $this->page->children(2)->published()->all();
        } else {
            if ($this->page->isLeaf() && ($this->page->level - $this->rootPage->level) > 1) {
                $levels = [$this->page->level, $this->page->level - 1];
            } else {
                $levels = [$this->page->level, $this->page->level + 1];
            }
            $items = $this->rootPage->children()->published()->andWhere(['level' => $levels])->all();
        }
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
                $prevModel = $model->getParent();
            }
        }
        // следущая страница
        if ($this->rootPage->equals($this->page)) {
            $nextModel = $model->children(1)->published()->one();
        } else {
            if (!($nextModel = $model->children(1)->published()->one() or $nextModel = $model->next()->published()->one())) {
                //пытаемся выйти наверх
                while(($parentModel = $model->getParent()) && !$parentModel->equals($this->rootPage)) {
                    if ($nextModel = $parentModel->next()->one()) break;
                    $model = $parentModel;
                }
            }
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
                $items[count($items) - 1]['items'] = $this->prepareItems($rawItems, $model->level, $hasActiveChild);
                $items[count($items) - 1]['hasActiveChild'] = $hasActiveChild;
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
            'page/guide' => Yii::t('gromver.platform', 'Default'),
        ];
    }
} 