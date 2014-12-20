<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use gromver\platform\basic\modules\news\models\Category;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * Class CategoryList
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategoryList extends Widget {
    /**
     * Category or CategoryId or CategoryId:CategoryPath
     * @var Category|string
     * @type modal
     * @url /grom/frontend/default/select-category
     * @translation gromver.platform
     */
    public $category;
    /**
     * @type list
     * @items layouts
     * @editable
     * @translation gromver.platform
     */
    public $layout = 'category/list';
    /**
     * @type list
     * @items itemLayouts
     * @editable
     * @translation gromver.platform
     */
    public $itemLayout = '_itemArticle';
    /**
     * @var string
     * @type list
     * @editable
     * @items sortColumns
     * @translation gromver.platform
     */
    public $sort = 'lft';
    /**
     * @var string
     * @type list
     * @editable
     * @items sortDirections
     * @translation gromver.platform
     */
    public $dir = SORT_DESC;
    /**
     * @ignore
     */
    public $listViewOptions = [];

    public function init()
    {
        parent::init();
    }

    protected function launch()
    {
        if ($this->category && !$this->category instanceof Category) {
            $this->category = Category::findOne(intval($this->category));
        }

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $this->category ? $this->category->children()->published()->orderBy(null) : Category::find()->roots()->published(),
                    'pagination' => false,
                    'sort' => [
                        'defaultOrder' => [$this->sort => intval($this->dir)]
                    ]
                ]),
            'itemLayout' => $this->itemLayout
        ]);
    }

    public function customControls()
    {
        return [
            [
                'url' => ['grom/news/backend/category/create', 'parentId' => $this->category ? $this->category->id : null, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-plus"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Create Category')]
            ],
            [
                'url' => ['grom/news/backend/category/index', 'CategorySearch' => ['parent_id' => $this->category ? $this->category->id : null]],
                'label' => '<i class="glyphicon glyphicon-th-list"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Categories list'), 'target' => '_blank']
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'category/list' => Yii::t('gromver.platform', 'Default'),
        ];
    }


    public static function itemLayouts()
    {
        return [
            '_itemArticle' => Yii::t('gromver.platform', 'Default'),
        ];
    }

    public static function sortColumns()
    {
        return [
            'published_at' => Yii::t('gromver.platform', 'By publish date'),
            'created_at' => Yii::t('gromver.platform', 'By create date'),
            'title' => Yii::t('gromver.platform', 'By name'),
            'lft' => Yii::t('gromver.platform', 'By order'),
        ];
    }

    public static function sortDirections()
    {
        return [
            SORT_ASC => Yii::t('gromver.platform', 'Asc'),
            SORT_DESC => Yii::t('gromver.platform', 'Desc'),
        ];
    }
} 