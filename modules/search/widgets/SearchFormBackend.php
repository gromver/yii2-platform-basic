<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\widgets;


use Yii;

/**
 * Class SearchFormFrontend
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchFormBackend extends SearchFormFrontend
{
    const EVENT_FETCH_ENDPOINTS = 'SearchEndpointsBackend';

    /**
     * @var string
     * @field list
     * @items url
     * @empty
     */
    public $url = '/grom/search/sql/backend/default/index';
}