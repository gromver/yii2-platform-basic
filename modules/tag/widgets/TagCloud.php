<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\tag\widgets;


use gromver\platform\basic\modules\tag\widgets\assets\TagAsset;
use gromver\platform\basic\modules\tag\models\Tag;
use gromver\platform\basic\modules\widget\widgets\Widget;
use yii\db\Query;
use Yii;

/**
 * Class TagCloud
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TagCloud extends Widget
{
    /**
     * @field list
     * @items languages
     * @translation gromver.platform
     */
    public $language;
    /**
     * @translation gromver.platform
     */
    public $fontBase = 14;
    /**
     * @translation gromver.platform
     */
    public $fontSpace = 6;

    private $_tags;
    private $_maxWeight;

    public function init()
    {
        parent::init();

        $this->language or $this->language = Yii::$app->language;

        $this->_tags = (new Query())
            ->select('t.id, t.title, t.alias, t.language, count(t2m.item_id) AS weight')
            ->from(Tag::tableName() . ' t')
            ->innerJoin(Tag::pivotTableName() . ' t2m', 't.id=t2m.tag_id')
            ->where(['t.language' => $this->language])
            ->groupBy('t.id')->all();

        shuffle($this->_tags);
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function getMaxWeight()
    {
        if(!isset($this->_maxWeight)) {
            $max = 0;
            array_walk($this->_tags, function($v)use(&$max){
                $max = max($v['weight'], $max);
            });
            $this->_maxWeight = $max;
        }

        return $this->_maxWeight;
    }

    protected function launch()
    {
        echo $this->render('tag/tagCloud', [
            'tags' => $this->_tags
        ]);

        $this->getView()->registerAssetBundle(TagAsset::className());
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/grom/tag/backend/default/create', 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-plus"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Create Tag')]
            ],
            [
                'url' => ['/grom/tag/backend/default/index'],
                'label' => '<i class="glyphicon glyphicon-th-list"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Tags list'), 'target' => '_blank']
            ],
        ];
    }

    public static function languages()
    {
        return ['' => Yii::t('gromver.platform', 'Autodetect')] + Yii::$app->getAcceptedLanguagesList();
    }
} 