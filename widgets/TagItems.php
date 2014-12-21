<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use gromver\platform\basic\modules\tag\models\Tag;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

/**
 * Class TagItems
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TagItems extends Widget {
    /**
     * Tag model or TagId
     * @var Tag|string
     * @type modal
     * @url /grom/frontend/default/select-tag
     * @translation gromver.platform
     */
    public $tag;
    /**
     * @translation gromver.platform
     */
    public $pageSize = 20;
    /**
     * @type list
     * @items layouts
     * @editable
     * @translation gromver.platform
     */
    public $layout = 'tag/itemsDefault';
    /**
     * @type list
     * @items itemLayouts
     * @editable
     * @translation gromver.platform
     */
    public $itemLayout = '_itemItem';

    protected function launch()
    {
        if (!$this->tag instanceof Tag) {
            $this->tag = Tag::findOne(intval($this->tag));
        }

        if (empty($this->tag)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Tag not found.'));
        }

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $this->tag->getTagToItems(),
                    'pagination' => [
                        'pageSize' => $this->pageSize
                    ]
                ]),
            'itemLayout' => $this->itemLayout,
            'model' => $this->tag
        ]);

        $this->tag->hit();
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/grom/tag/backend/default/update', 'id' => $this->tag->id, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Update Tag')]
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'tag/itemsDefault' => Yii::t('gromver.platform', 'Default'),
            'tag/itemsList' => Yii::t('gromver.platform', 'List'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemItem' => Yii::t('gromver.platform', 'Default'),
        ];
    }
}