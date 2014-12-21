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
 * Class PageView
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageView extends Widget {
    /**
     * Page model or PageId or PageId:PageAlias
     * @var Page|string
     * @type modal
     * @url /grom/frontend/default/select-page
     * @translation gromver.platform
     */
    public $page;
    /**
     * @type list
     * @items layouts
     * @translation gromver.platform
     */
    public $layout = 'page/article';
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
        if ($this->page && !$this->page instanceof Page) {
            $this->page = Page::findOne(intval($this->page));
        }

        if (empty($this->page)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Page not found.'));
        }

        if ($this->useHighlights) {
            CkeditorHighlightAsset::register($this->getView());
        }

        echo $this->render($this->layout, [
            'model' => $this->page
        ]);
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
            'page/article' => Yii::t('gromver.platform', 'Article'),
            'page/content' => Yii::t('gromver.platform', 'Content'),
        ];
    }
} 