<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use gromver\platform\basic\modules\elasticsearch\models\ActiveDocument;
use gromver\platform\basic\modules\elasticsearch\models\ElasticSearch;
use Yii;
use yii\caching\Cache;
use yii\data\ActiveDataProvider;
use yii\di\Instance;

/*
 * $query->query = [
    'filtered' => [
        'filter' => [
            'and' => [
                [
                    'not' => [
                        'and' => [
                            [
                                'exists' => ['field' => 'published']
                            ],
                            [
                                'term' => ['published' => false]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
 */

/**
 * Class SearchResults
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property array $filters
 */
class SearchResults extends Widget {
    /**
     * @ignore
     */
    public $types;
    public $query;
    /**
     * @type list
     * @items languages
     */
    public $language;
    /**
     * @var array
     * @ignore
     */
    public $highlight = [
        'fields' => [
            'title' => ["type" => "plain", 'no_match_size' => 150],
            'text' => ["type" => "plain", 'no_match_size' => 150]
        ]
    ];
    /**
     * @type list
     * @items caches
     * @editable
     * @empty no cache
     */
    public $cache;
    public $cacheDuration = 3600;
    /**
     * @type list
     * @items layouts
     * @editable
     */
    public $layout = 'search/results';
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
    private $_filters;


    public function init()
    {
        parent::init();

        if (!isset($this->types)) {
            $this->types = ActiveDocument::registeredTypes();
        }

        ElasticSearch::getDb()->open();    //проверяем коннект к elasticSearch
    }

    protected function launch()
    {
        $query = ElasticSearch::find();
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
            $query->query['filtered']['query']['multi_match'] = ['query' => $this->query, 'fields' => ['_all']];
        }

        if (!empty($this->language)) {
            $query->query['filtered']['filter']['and']['filters'][] = [
                'and' => [
                    [
                        'exists' => ['field' => 'language']
                    ],
                    [
                        'term' => ['language' => $this->language]
                    ]
                ]
            ];
        }

        //чтоб в ActiveQuery задать фильтр по типу надо обязательно задать фильтр по индексу
        //$query->index = 'grom';
        //$query->type = 'page';

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

    protected function collectFilters()
    {
        $filters = [];
        foreach ($this->types as $type) {
            if ($documentClass = ActiveDocument::findDocumentByType($type)) {
                /** @var ActiveDocument $documentClass */
                $conditions = $documentClass::filter();
                foreach ($conditions as $condition) {
                    $filters[json_encode($condition)] = $condition;
                }
            }
        }

        return array_values($filters);
    }

    /**
     * @param $value
     */
    public function setFilters($value)
    {
        $this->_filters = $value;
    }
    /**
     * @return array|mixed
     */
    protected function getFilters()
    {
        if (!isset($this->_filters)) {
            if ($this->cache) {
                /** @var Cache $cache */
                $cache = Instance::ensure($this->cache, Cache::className());
                $this->_filters = $cache->get([__CLASS__, $this->types]);
                if ($this->_filters === false) {
                    $this->_filters = $this->collectFilters();
                    $cache->set([__CLASS__, $this->types], $this->_filters, $this->cacheDuration);
                }
            } else {
                $this->_filters = $this->collectFilters();
            }
        }

        return $this->_filters;
    }

    public static function caches()
    {
        return [
            'cache' => 'cache'
        ];
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

    public static function languages()
    {
        return ['' => Yii::t('gromver.platform', 'Autodetect')] + Yii::$app->getAcceptedLanguagesList();
    }
}