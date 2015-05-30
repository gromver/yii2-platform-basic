<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\tag\controllers\backend;


use gromver\platform\basic\modules\tag\models\Tag;
use gromver\platform\basic\modules\tag\models\TagSearch;
use gromver\widgets\ModalIFrame;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\Response;

/**
 * Class DefaultController implements the CRUD actions for Tag model.
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
                    'publish' => ['post'],
                    'unpublish' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'publish', 'unpublish', 'delete', 'index', 'view', 'select', 'tag-list', 'tag-group-list'],
                        'roles' => ['readTag'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createTag'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['publish', 'unpublish'],
                        'roles' => ['updateTag'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['bulk-delete'],
                        'roles' => ['deleteTag'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Tag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TagSearch();
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
    public function actionSelect($route = 'grom/tag/frontend/default/view')
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        Yii::$app->grom->applyModalLayout();

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'route' => $route
        ]);
    }

    /**
     * Displays a single Tag model.
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
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string|null $language
     * @param string|null $sourceId
     * @param string|null $backUrl
     * @param bool|null $modal
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($sourceId = null, $language = null, $backUrl = null, $modal = null)
    {
        $model = new Tag();
        $model->loadDefaultValues();
        $model->language = $language ? $language : Yii::$app->language;
        $model->status = Tag::STATUS_PUBLISHED;

        if ($sourceId && $language) {
            $sourceModel = $this->findModel($sourceId);
            $model->language = $language;
            $model->translation_id = $sourceModel->translation_id;
            $model->alias = $sourceModel->alias;
            $model->status = $sourceModel->status;
            $model->metakey = $sourceModel->metakey;
            $model->metadesc = $sourceModel->metadesc;
        } else {
            $sourceModel = null;
        }

        if ($modal) {
            Yii::$app->grom->applyModalLayout();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($modal) {
                ModalIFrame::postData([
                    'id' => $model->id,
                    'title' => $model->title,
                    'description' => Yii::t('gromver.platform', 'Tag: {title}', ['title' => $model->title]),
                    'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                    'value' => $model->id . ':' . $model->alias
                ]);
            }

            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'sourceModel' => $sourceModel
            ]);
        }
    }

    /**
     * Updates an existing Tag model.
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

        if (!Yii::$app->user->can('updateTag', ['tag' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Tag model.
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

        if (!Yii::$app->user->can('deleteTag', ['tag' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->delete();

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

        $models = Tag::findAll(['id'=>$data]);

        foreach($models as $model) {
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

        if (!Yii::$app->user->can('updateTag', ['tag' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->status = Tag::STATUS_PUBLISHED;
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

        if (!Yii::$app->user->can('updateTag', ['tag' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->status = Tag::STATUS_UNPUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * Отдает список тегов для Select2 виджета
     * @param string|null $q
     * @param string|null $language
     * @return array
     */
    public function actionTagList($q = null, $language = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $results = Tag::find()->select('id, title AS text')->andFilterWhere(['like', 'title', urldecode($q)])->andFilterWhere(['language' => $language])->limit(20)->asArray()->all();

        return ['results' => $results];
    }

    /**
     * Отдает список групп тегов для Select2 виджета
     * @param string|null $q
     * @return array
     */
    public function actionTagGroupList($q = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $results = Tag::find()->select('group AS id, group AS text')->andFilterWhere(['like', 'group', urldecode($q)])->groupBy('group')->limit(20)->asArray()->all();

        return ['results' => $results];
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
