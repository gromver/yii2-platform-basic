<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\news\widgets;


use gromver\platform\basic\modules\news\models\Category;
use gromver\platform\basic\modules\news\models\Post;
use gromver\platform\basic\modules\widget\widgets\Widget;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * Class PostDay
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostDay extends Widget
{
    /**
     * @var Category|string
     * @field modal
     * @url /grom/frontend/default/select-category
     * @translation gromver.platform
     */
    public $category;
    /**
     * @translation gromver.platform
     */
    public $year;
    /**
     * @translation gromver.platform
     */
    public $month;
    /**
     * @translation gromver.platform
     */
    public $day;
    /**
     * @field list
     * @items layouts
     * @translation gromver.platform
     */
    public $layout = 'post/day';
    /**
     * @field list
     * @items itemLayouts
     * @translation gromver.platform
     */
    public $itemLayout = '_itemIssue';

    /**
     * @field list
     * @editable
     * @items sortColumns
     * @var string
     * @translation gromver.platform
     */
    public $sort = 'published_at';

    /**
     * @field list
     * @editable
     * @items sortDirections
     * @translation gromver.platform
     */
    public $dir = SORT_ASC;

    /**
     * @ignore
     */
    public $listViewOptions = [];

    protected function launch()
    {
        if ($this->category && !$this->category instanceof Category) {
            $this->category = Category::findOne(intval($this->category));
        }

        $categoryId = $this->category ? $this->category->id : null;

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => Post::find()->published()->category($categoryId)->day($this->year, $this->month, $this->day)->last(),
                    'pagination' => false,
                    'sort' => [
                        'defaultOrder' => [$this->sort => (int)$this->dir]
                    ]
                ]),
            'itemLayout' => $this->itemLayout,
            'prevDayPost' => Post::find()->published()->category($categoryId)->beforeDay($this->year, $this->month, $this->day)->last()->one(),
            'nextDayPost' => Post::find()->published()->category($categoryId)->afterDay($this->year, $this->month, $this->day)->last()->one(),
            'category' => $this->category,
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'listViewOptions' => $this->listViewOptions
        ]);
    }

    public static function layouts()
    {
        return [
            'post/day' => Yii::t('gromver.platform', 'Default'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemArticle' => Yii::t('gromver.platform', 'Article'),
            '_itemIssue' => Yii::t('gromver.platform', 'Issue'),
        ];
    }

    public static function sortColumns()
    {
        return [
            'published_at' => Yii::t('gromver.platform', 'By publish date'),
            'created_at' => Yii::t('gromver.platform', 'By create date'),
            'title' => Yii::t('gromver.platform', 'By name'),
            'ordering' => Yii::t('gromver.platform', 'By order'),
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