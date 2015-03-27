<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\behaviors;


use gromver\modulequery\Event;

/**
 * Class SearchBehaviorEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchBehaviorEvent extends Event {
    /**
     * @var \yii\db\ActiveRecord|\gromver\platform\basic\interfaces\model\ViewableInterface|\gromver\platform\basic\interfaces\model\SearchableInterface
     */
    public $model;
} 