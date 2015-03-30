<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\modules\sql\widgets\events;


/**
 * Class SqlBeforeSearchEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender \gromver\platform\basic\modules\search\modules\sql\widgets\SearchResultsFrontend|\gromver\platform\basic\modules\search\modules\sql\widgets\SearchResultsBackend
 */
class SqlBeforeSearchEvent extends \gromver\modulequery\Event {
    /**
     * @var \yii\db\Query
     */
    public $query;
    /**
     * @var bool
     */
    public $skip = false;
}