<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\widgets;


use gromver\modulequery\ModuleEvent;
use gromver\platform\basic\modules\search\widgets\events\SearchEndpointsEvent;
use gromver\platform\basic\modules\widget\widgets\Widget;
use Yii;

/**
 * Class SearchFormFrontend
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchFormFrontend extends Widget
{
    const EVENT_FETCH_ENDPOINTS = 'SearchEndpointsFrontend';

    /**
     * @var string
     * @field list
     * @items url
     * @empty
     */
    public $url = '/grom/search/sql/frontend/default/index';
    /**
     * @var string
     * @ignore
     */
    public $queryParam = 'q';
    /**
     * @var string
     * @field list
     * @items layouts
     * @editable
     */
    public $layout = 'form/default';
    /**
     * @var string
     * @ignore
     */
    public $query;
    /**
     * @var array
     * @ignore
     */
    public $options = ['class' => 'search-form', 'role' => 'search'];

    protected function launch()
    {
        echo $this->render($this->layout, [
            'url' => $this->url,
            'query' => $this->query,
            'options' => $this->options
        ]);
    }

    public static function url()
    {
        return ModuleEvent::trigger(static::EVENT_FETCH_ENDPOINTS, new SearchEndpointsEvent([
            'items' => [],
        ]), 'items');
    }

    public static function layouts()
    {
        return [
            'form/default' => Yii::t('gromver.platform', 'Default'),
        ];
    }
} 