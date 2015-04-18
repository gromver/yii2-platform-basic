<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\behaviors\SearchBehavior;
use gromver\platform\basic\interfaces\model\SearchableInterface;
use gromver\platform\basic\interfaces\model\ViewableInterface;
use yii\base\InvalidConfigException;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    /**
     * Инедксация сохраненой модели для последующего поиска по этому индексу
     * @param $event \gromver\platform\basic\behaviors\SearchBehaviorEvent
     * @throws InvalidConfigException
     */
    public function indexPage($event)
    {
        if (!$event->model instanceof SearchableInterface) {
            throw new InvalidConfigException('\gromver\platform\basic\behaviors\SearchBehaviorEvent::model must be an instance of \gromver\platform\basic\interfaces\model\SearchableInterface.');
        }

        if (!$event->model instanceof ViewableInterface) {
            throw new InvalidConfigException('\gromver\platform\basic\behaviors\SearchBehaviorEvent::model must be an instance of \gromver\platform\basic\interfaces\model\ViewableInterface.');
        }
    }

    /**
     * Удаление записи из индекса соответсвующей удаленной модели
     * @param \gromver\platform\basic\behaviors\SearchBehaviorEvent $event
     * @return bool|null
     * @throw InvalidParamException
     */
    public function deletePage($event) {}

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            SearchBehavior::EVENT_INDEX_PAGE => [$this, 'indexPage'],
            SearchBehavior::EVENT_DELETE_PAGE => [$this, 'deletePage'],
        ];
    }
} 