<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use yii\caching\Cache;
use yii\di\Instance;

/**
 * Class WidgetCacheTrait
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
trait WidgetCacheTrait {
    /**
     * @var Cache|string
     * @field list
     * @items caches
     * @before <h3>Caching</h3>
     * @label Cache Component
     * @translation gromver.platform
     */
    public $cache = 'cache';
    /**
     * @var integer
     * @label Cache Duration
     * @translation gromver.platform
     */
    public $cacheDuration = 3600;
    /**
     * @var \yii\caching\Dependency
     * @ignore
     */
    public $cacheDependency;

    protected function ensureCache()
    {
        if (isset($this->cache)) {
            $this->cache = $this->cache ? Instance::ensure($this->cache, Cache::className()) : null;
        }

        return $this->cache;
    }

    public static function caches()
    {
        return [
            \Yii::t('gromver.platform', 'No cache'),
            'cache' => 'cache'
        ];
    }
} 