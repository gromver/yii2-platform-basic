<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page\controllers\backend;


use gromver\platform\basic\modules\main\models\DbState;
use gromver\platform\basic\modules\page\models\Page;
use gromver\platform\basic\modules\page\models\PageSearch;
use kartik\alert\Alert;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\Response;

/**
 * Class DefaultController implements the CRUD actions for Page model.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \gromver\platform\basic\components\BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'delete'],
                    'bulk-delete' => ['post'],
                    'delete-file' => ['post'],
                    'publish' => ['post'],
                    'unpublish' => ['post'],
                    'ordering' => ['post'],
                    'pages' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'publish', 'unpublish', 'delete', 'index', 'view', 'select', 'pages', 'page-list'],
                        'roles' => ['readPage'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createPage'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['ordering'],
                        'roles' => ['updatePage'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['bulk-delete'],
                        'roles' => ['deletePage'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param string $route
     * @return string
     */
    public function actionSelect($route = 'grom/page/frontend/default/view')
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        Yii::$app->grom->applyModalLayout();

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'route' => $route
        ]);
    }

    /**
     * Отдает список страниц для Select2 виджета
     * @param string|null $q
     * @param string|null $language
     * @param integer|null $exclude
     * @return array
     */
    public function actionPageList($q = null, $language = null, $exclude = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $query = Page::find()->excludeRoots();
        if ($exclude && $page = Page::findOne($exclude)) {
            /** @var $page Page */
            $query->excludePage($page);
        }

        $results = $query->select('id, title AS text')->andFilterWhere(['like', 'title', urldecode($q)])->andFilterWhere(['language' => $language])->limit(20)->asArray()->all();

        return ['results' => $results];
    }

    /**
     * Displays a single Page model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string|null $language
     * @param string|null $sourceId
     * @param string|null $parentId
     * @param string|null $backUrl
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($sourceId = null, $parentId = null, $language = null, $backUrl = null)
    {
        $model = new Page();
        $model->loadDefaultValues();
        $model->status = Page::STATUS_PUBLISHED;
        $model->language = $language ? $language : Yii::$app->language;

        if (isset($parentId)) {
            $parentCategory = $this->findModel($parentId);
            $model->parent_id = $parentCategory->id;
            if ($parentCategory->language) {
                $model->language = $parentCategory->language;
            }
        }

        if($sourceId && $language) {
            $sourceModel = $this->findModel($sourceId);
            /** @var Page $parentItem */
            // если локализуемая тсраница имеет родителя, то пытаемся найти релевантную локализацию для родителя создаваемой страницы
            if (!($sourceModel->level > 2 && $parentItem = @$sourceModel->parent->translations[$language])) {
                $parentItem = Page::find()->roots()->one();
            }

            $model->parent_id = $parentItem->id;
            $model->language = $language;
            $model->translation_id = $sourceModel->translation_id;
            $model->alias = $sourceModel->alias;
            $model->status = $sourceModel->status;
            $model->preview_text = $sourceModel->preview_text;
            $model->detail_text = $sourceModel->detail_text;
            $model->metakey = $sourceModel->metakey;
            $model->metadesc = $sourceModel->metadesc;
        } else {
            $sourceModel = null;
        }

        if ($model->load(Yii::$app->request->post()) && $model->saveNode()) {
            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'sourceModel' => $sourceModel
            ]);
        }
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param null $backUrl
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $backUrl = null)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('updatePage', ['page' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->saveNode()) {
            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('deletePage', ['page' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->children()->count()) {
            Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', "It's impossible to remove category ID:{id} to contain in it subcategories so far.", ['id' => $model->id]));
        } else {
            $model->delete();
        }

        if(Yii::$app->request->getIsDelete()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
        }

        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function actionBulkDelete()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = Page::find()->where(['id' => $data])->orderBy(['lft' => SORT_DESC])->all();

        foreach ($models as $model) {
            /** @var Page $model */
            if ($model->children()->count()) continue;

            $model->delete();
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionPublish($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('updatePage', ['page' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->status = Page::STATUS_PUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUnpublish($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('updatePage', ['page' => $model])) {
            throw new ForbiddenHttpException('You have no rights to update this page.');
        }

        $model->status = Page::STATUS_UNPUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @return Response
     */
    public function actionOrdering()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        foreach ($data as $id => $order) {
            if ($target = Page::findOne($id)) {
                $target->updateAttributes(['ordering' => intval($order)]);
            }
        }

        Page::find()->roots()->one()->reorderNode('ordering');
        DbState::updateState(Page::tableName());

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
