<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\widget\widgets;


use yii\caching\Cache;
use yii\di\Instance;

/**
 * Class WidgetCacheTrait
 * Использование
 * class MyWidget extends \gromver\platform\basic\modules\widget\widgets\Widget {
 *      use WidgetCacheTrait
 *
 *      protected function launch()
 *      {
 *          $cache = $this->ensureCache();
 *          if ($cache) {
 *              if (($result = $cache->get($id)) === false) {
 *                  $result = $this->getResultMethod();
 *                  $cache->set($id, $result, $this->cacheDuration, $this->cacheDependency);
 *              }
 *          } else {
 *              $result = $this->getResultMethod();
 *          }
 *      }
 * }
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
trait WidgetCacheTrait {
    /**
     * @var Cache|string
     * @field list
     * @items caches
     * @before <h3 class="col-sm-offset-3 col-sm-9">Caching</h3>
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

    /**
     * @return null|Cache
     * @throws \yii\base\InvalidConfigException
     */
    protected function ensureCache()
    {
        return $this->cache ? Instance::ensure($this->cache, Cache::className()) : null;
    }

    public static function caches()
    {
        return [
            \Yii::t('gromver.platform', 'No cache'),
            'cache' => 'cache',
        ];
    }
} 