<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use gromver\modulequery\ModuleEvent;
use gromver\platform\basic\modules\elasticsearch\models\Index;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class SearchResultsElasticsearch
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property array $filters
 */
class SearchResultsElasticsearch extends Widget {
    const EVENT_BEFORE_SEARCH = 'ElasticsearchBeforeSearch_';

    /**
     * Модели по которым будет производится поиск
     * @ignore
     * @var string[]
     */
    public $models;
    public $query;
    /**
     * Настройка хайлайта
     * @var array
     * @ignore
     */
    public $highlight = [
        'fields' => [
            'title' => ["type" => "plain", 'no_match_size' => 200],
            'content' => ["type" => "plain", 'no_match_size' => 200]
        ]
    ];
    /**
     * @type list
     * @items layouts
     * @editable
     */
    public $layout = 'searchElasticsearch/results';
    /**
     * @type list
     * @items itemLayouts
     * @editable
     */
    public $itemLayout = '_itemDefault';
    public $pageSize = 10;
    /**
     * Массив с фильтрами для поиска, к данным фильтрам применяется условие AND
     * если фильтры не заданы то виджет мерджит фильтры по умолчанию от каждого ActiveDocument известного cms
     * @ignore
     * @var null|array
     */
    public $filters = [];
    /**
     * Определяет какие использовать ссылки: на фронт или бэкенд
     * @ignore
     * @var string
     */
    public $frontendMode = true;

    public function init()
    {
        parent::init();

        Index::getDb()->open();    // проверяем коннект к elasticSearch
    }

    protected function launch()
    {
        $query = Index::find();

        if ($this->models) {
            // ищем только по тем моделям которые в списке
            $this->filters[] = ['terms' => ['model_class' => $this->models]];
            // ивент на модификацию фильтров ($this->filters) сторонними модулями
            foreach ($this->models as $modelClass) {
                ModuleEvent::trigger(self::EVENT_BEFORE_SEARCH . $modelClass, [$query, $this]);
            }
        }

        $query->query = [
            'filtered' => [
                'filter' => [
                    'and' => [
                        'filters' => $this->filters,
                        //'_cache' => true,
                        //'_cache_key' =>
                    ]
                ]
            ],
        ];

        if (!empty($this->query)) {
            $query->query['filtered']['query']['multi_match'] = ['query' => $this->query, 'fields' => ['title', 'content', 'tags']];
        }

        $query->highlight = $this->highlight;

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => $this->pageSize
                    ]
                ]),
            'itemLayout' => $this->itemLayout
        ]);
    }

    public static function layouts()
    {
        return [
            'search/results' => Yii::t('gromver.platform', 'Default'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemDefault' => Yii::t('gromver.platform', 'Default'),
        ];
    }
}