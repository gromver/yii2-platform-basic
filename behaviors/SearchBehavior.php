<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\behaviors;


use gromver\modulequery\ModuleEvent;
use yii\db\ActiveRecord;

/**
 * Class SearchBehavior
 * Данное поведение инициирует соответсвующие модульные события [[ModuleEvent::trigger]] после удаления/записи модели
 * Эти события используют поисковые модули (Sql, Elasticsearch) для построения поискового индекса
 * Важно: модель использующее данное поведение должна наследовать \gromver\platform\basic\interfaces\model\ViewableInterface и \gromver\platform\basic\interfaces\model\SearchableInterface
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchBehavior extends \yii\base\Behavior
{
    const EVENT_INDEX_PAGE = 'SearchBehavior_IndexPage';
    const EVENT_DELETE_PAGE = 'SearchBehavior_DeletePage';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'searchIndexPage',
            ActiveRecord::EVENT_AFTER_UPDATE => 'searchIndexPage',
            ActiveRecord::EVENT_AFTER_DELETE => 'searchDeletePage',
        ];
    }

    /**
     * @param $event \yii\base\Event
     */
    public function searchIndexPage($event)
    {
        ModuleEvent::trigger(self::EVENT_INDEX_PAGE, new SearchBehaviorEvent([
            'model' => $this->owner
        ]));
    }

    /**
     * @param $event \yii\base\Event
     */
    public function searchDeletePage($event)
    {
        ModuleEvent::trigger(self::EVENT_DELETE_PAGE, new SearchBehaviorEvent([
            'model' => $this->owner
        ]));
    }
} 