<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\modules\elastic\widgets\events;


/**
 * Class ElasticBeforeSearchEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\basic\modules\search\modules\elastic\widgets\SearchResultsFrontend $sender
 */
class ElasticBeforeSearchEvent extends \gromver\modulequery\Event {
    /**
     * @var \yii\elasticsearch\Query
     */
    public $query;
    /**
     * @var bool
     */
    public $skip = false;
}