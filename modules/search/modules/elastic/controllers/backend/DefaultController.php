<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\modules\elastic\controllers\backend;


use gromver\modulequery\ModuleEvent;
use gromver\platform\basic\behaviors\TaggableBehavior;
use gromver\platform\basic\modules\search\modules\elastic\events\ElasticIndexEvent;
use gromver\platform\basic\modules\search\modules\elastic\models\Index;
use gromver\platform\basic\modules\search\modules\elastic\Module;
use gromver\platform\basic\modules\search\widgets\SearchResultsBackend;
use gromver\platform\basic\modules\search\widgets\SearchResultsFrontend;
use yii\elasticsearch\ActiveRecord;
use yii\elasticsearch\Exception;
use yii\filters\AccessControl;
use yii\helpers\Json;
use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \gromver\platform\basic\components\BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['read'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['reindex'],
                        'roles' => ['update'],
                    ]
                ]
            ]
        ];
    }

    public function actionIndex($q = null)
    {
        return $this->render('index', [
            'query' => $q
        ]);
    }

    public function actionReindex()
    {
        $models = array_unique(array_merge(array_keys(SearchResultsBackend::models()), array_keys(SearchResultsFrontend::models())));

        // todo очистку индекса

        $output = '';
        foreach ($models as $class) {
            $output .= "Uploading \"{$class}\" model items.<br />";
            $completed = $this->upload($class);
            $output .= "<b>{$completed} items uploaded.</b><hr /><br />";
        }

        return $this->renderContent($output);
    }

    /**
     * @param $class \yii\db\ActiveRecord|string
     * @return int
     * @throws \yii\elasticsearch\Exception
     */
    public function upload($class)
    {
        $bulk = '';
        $index = Index::index();
        $type = Index::type();
        $timestamp = time();

        /** @var \yii\base\Behavior[] */
        $behaviors = (new $class)->behaviors;
        $query = $class::find();
        array_walk($behaviors, function($behavior) use ($query) {
            if ($behavior instanceof TaggableBehavior) {
                $query->with('tags');
            }
        });

        foreach ($query->each() as $model) {
            /** @var \yii\db\ActiveRecord|\gromver\platform\basic\interfaces\model\SearchableInterface|\gromver\platform\basic\interfaces\model\ViewableInterface $model */
            $action = Json::encode([
                "index" => [
                    "_id" => $model->getPrimaryKey(),
                    "_type" => $type,
                    "_index" => $index,
                ],
            ]);

            $indexModel = Index::findOne(['model_id' => $model->getPrimaryKey(), 'model_class' => $model->className()]) or $indexModel = new Index();
            $indexModel->model_id = $model->getPrimaryKey();
            $indexModel->model_class = $model->className();
            $indexModel->title = $model->getSearchTitle();
            $indexModel->content = $model->getSearchContent();
            $indexModel->tags = $model->getSearchTags();
            $indexModel->url_backend = $model->getBackendViewLink();
            $indexModel->url_frontend = $model->getFrontendViewLink();
            $indexModel->updated_at = $timestamp;

            ModuleEvent::trigger(Module::EVENT_BEFORE_CREATE_INDEX . $model->className(), new ElasticIndexEvent([
                'model' => $model,
                'index' => $indexModel,
                'sender' => $this->module,
            ]));
            
            if ($indexModel->validate()) {
                $bulk .= $action . "\n" . Json::encode($indexModel->toArray()) . "\n";
            }
        }

        $url = [$index, $type, '_bulk'];
        $response = ActiveRecord::getDb()->post($url, [], $bulk);
        $n = 0;
        $errors = [];
        foreach ($response['items'] as $item) {
            if (isset($item['index']['status']) && ($item['index']['status'] == 201 || $item['index']['status'] == 200)) {
                $n++;
            } else {
                $errors[] = $item['index'];
            }
        }
        if (!empty($errors) || isset($response['errors']) && $response['errors']) {
            throw new Exception(__METHOD__ . ' failed inserting '. $class .' model records.', $errors);
        }

        return $n;
    }
}
