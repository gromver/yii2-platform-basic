<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


use gromver\modulequery\ModuleEvent;
use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\behaviors\SearchBehavior;
use gromver\platform\basic\components\events\SearchableModelsEvent;
use gromver\platform\basic\interfaces\model\SearchableInterface;
use gromver\platform\basic\interfaces\model\ViewableInterface;
use yii\base\InvalidParamException;

/**
 * Class BaseSearchModule
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class BaseSearchModule extends \yii\base\Module implements ModuleEventsInterface
{
    // <-- лишнее
    const EVENT_FRONTEND_SEARCHABLE_MODELS = 'SearchableModelsFrontend';
    const EVENT_BACKEND_SEARCHABLE_MODELS = 'SearchableModelsBackend';

    public $frontendSearchableModels = [
        'gromver\platform\basic\modules\page\models\Page',
        'gromver\platform\basic\modules\news\models\Post',
        'gromver\platform\basic\modules\news\models\Category',
    ];

    public $backendSearchableModels = [
        'gromver\platform\basic\modules\page\models\Page',
        'gromver\platform\basic\modules\news\models\Post',
        'gromver\platform\basic\modules\news\models\Category',
    ];

    public function getFrontendSearchableModels()
    {
        $event = new SearchableModelsEvent([
            'models' => $this->frontendSearchableModels
        ]);
        ModuleEvent::trigger(self::EVENT_FRONTEND_SEARCHABLE_MODELS, $event);

        return $event->models;
    }

    public function getBackendSearchableModels()
    {
        $event = new SearchableModelsEvent([
            'models' => $this->backendSearchableModels
        ]);
        ModuleEvent::trigger(self::EVENT_BACKEND_SEARCHABLE_MODELS, $event);

        return $event->models;
    }
    // лишнее -->

    /**
     * Инедксация сохраненой модели для последующего поиска по этому индексу
     * @param \gromver\platform\basic\behaviors\SearchBehaviorEvent $event
     * @return bool|null
     * @throw InvalidParamException
     */
    public function indexPage($event)
    {
        if (!$event->model instanceof SearchableInterface) {
            throw new InvalidParamException(__CLASS__ . '::indexPage($event). $event->model must be an \gromver\platform\basic\interfaces\model\SearchableInterface instance.');
        }

        if (!$event->model instanceof ViewableInterface) {
            throw new InvalidParamException(__CLASS__ . '::indexPage($event). $event->model must be an \gromver\platform\basic\interfaces\model\ViewableInterface instance.');
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