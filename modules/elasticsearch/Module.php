<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\elasticsearch;

use gromver\modulequery\ModuleEvent;
use gromver\platform\basic\components\BaseSearchModule;
use gromver\platform\basic\interfaces\module\DesktopInterface;
use gromver\platform\basic\interfaces\module\MenuItemRoutesInterface;
use gromver\platform\basic\interfaces\module\MenuRouterInterface;
use gromver\platform\basic\modules\elasticsearch\components\MenuRouterSearch;
use gromver\platform\basic\modules\elasticsearch\models\Index;
use kartik\widgets\Alert;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends BaseSearchModule implements DesktopInterface, MenuItemRoutesInterface, MenuRouterInterface
{
    const EVENT_BEFORE_CREATE_INDEX = 'ElasticsearchBeforeCreateIndex_';
    const EVENT_BEFORE_DELETE_INDEX = 'ElasticsearchBeforeDeleteIndex_';

    public $controllerNamespace = 'gromver\platform\basic\modules\elasticsearch\controllers';
    public $defaultRoute = 'frontend/default';
    public $desktopOrder = 6;
    public $elasticsearchIndex;

    public function init()
    {
        parent::init();

        if (!isset($this->elasticsearchIndex)) {
            throw new InvalidConfigException(__CLASS__ . '::elasticsearchIndex must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Elastic Search'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Search'), 'url' => ['/' . $this->getUniqueId() . '/backend/default/index']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Elastic Search'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Search'), 'route' => $this->getUniqueId() . '/frontend/default/index'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterSearch::className();
    }

    /**
     * @inheritdoc
     */
    public function indexPage($model)
    {
        $index = Index::findOne(['model_id' => $model->getPrimaryKey(), 'model_class' => $model->className()]) or $index = new Index();
        $index->model_id = $model->getPrimaryKey();
        $index->model_class = $model->className();
        $index->title = $model->getSearchTitle();
        $index->content = $model->getSearchContent();
        $index->tags = $model->getSearchTags();
        $index->url_backend = $model->getBackendViewLink();
        $index->url_frontend = $model->getFrontendViewLink();

        ModuleEvent::trigger(self::EVENT_BEFORE_CREATE_INDEX . $model->className(), [$index, $model]);
        if (!$index->save()) {
            Yii::$app->session->setFlash(Alert::TYPE_DANGER, implode($index->getErrors(), "\n"));
            Yii::error('Unable to index model ' . $model->className() . '::' . $model->getPrimaryKey() . ', error: ' . implode($index->getErrors(), "\n"));
        }
    }

    /**
     * @inheritdoc
     */
    public function deletePage($model)
    {
        $index = Index::find()->where(['model_id' => $model->getPrimaryKey(), 'model_class' => $model->className()])->one();
        ModuleEvent::trigger(self::EVENT_BEFORE_DELETE_INDEX . $model->className(), [$index, $model]);
        if ($index) {
            $index->delete();
        }
    }
}
