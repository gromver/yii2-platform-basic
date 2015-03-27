<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


use gromver\modulequery\ModuleEventsInterface;
use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\behaviors\SearchBehavior;
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
        return array_merge($this->frontendSearchableModels, ModuleQuery::instance()->implement('\gromver\platform\basic\interfaces\SearchableModelsInterface')->fetch('getFrontendSearchableModels', [], ModuleQuery::AGGREGATE_MERGE));
    }

    public function getBackendSearchableModels()
    {
        return array_merge($this->backendSearchableModels, ModuleQuery::instance()->implement('\gromver\platform\basic\interfaces\SearchableModelsInterface')->fetch('getBackendSearchableModels', [], ModuleQuery::AGGREGATE_MERGE));
    }

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
} 