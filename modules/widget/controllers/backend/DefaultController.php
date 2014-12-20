<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\backend\modules\widget\controllers;

use gromver\models\ObjectModel;
use gromver\widgets\ModalIFrame;
use gromver\platform\basic\widget\models\WidgetConfig;
use gromver\platform\basic\widget\models\WidgetConfigSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class DefaultController implements the CRUD actions for WidgetConfig model.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'delete'],
                    'bulk-delete' => ['post'],
                    'configure' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'configure'],
                        'roles' => ['update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'bulk-delete'],
                        'roles' => ['delete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['read'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all WidgetConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WidgetConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WidgetConfig model.
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
     * Creates a new WidgetConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WidgetConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WidgetConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WidgetConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if(Yii::$app->request->getIsDelete())
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));

        return $this->redirect(['index']);
    }

    public function actionBulkDelete()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = WidgetConfig::findAll(['id'=>$data]);

        foreach($models as $model)
            $model->delete();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    public function actionConfigure($modal=null)
    {
        if (!($widget_id = Yii::$app->request->getBodyParam('widget_id')))
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget ID isn't specified."));

        if (!($widget_class = Yii::$app->request->getBodyParam('widget_class')))
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget Class isn't specified."));

        if (($widget_context = Yii::$app->request->getBodyParam('widget_context'))===null)
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget Context isn't specified."));

        $selected_context = Yii::$app->request->getBodyParam('selected_context', $widget_context);

        $task = Yii::$app->request->getBodyParam('task');

        if (($url = Yii::$app->request->getBodyParam('url'))===null)
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget page url isn't specified."));
        //$url = Yii::$app->request->getBodyParam('url', Yii::$app->request->getReferrer());

        if ($task == 'delete') {
            if(Yii::$app->request->getBodyParam('bulk-method')) {
                foreach (WidgetConfig::find()->where('widget_id=:widget_id AND context>=:context AND language=:language', [
                    ':widget_id' => $widget_id,
                    ':context' => $selected_context,
                    ':language' => Yii::$app->language
                ])->each() as $configModel) {
                    /** @var WidgetConfig $configModel */
                    $configModel->delete();
                }
            } elseif ($configModel = WidgetConfig::findOne([
                'widget_id' => $widget_id,
                'context' => $selected_context,
                'language' => Yii::$app->language
            ])) {
                $configModel->delete();
            }
        }

        $widget_config = Yii::$app->request->getBodyParam('widget_config', '[]');
        $widgetConfig = Json::decode($widget_config);
        $widgetConfig['id'] = $widget_id;
        $widgetConfig['context'] = $selected_context;
        $widget = new $widget_class($widgetConfig);

        $model = new ObjectModel($widget);

        if (($task=='save' || $task=='refresh') && $model->load(Yii::$app->request->post())) {
            if ($model->validate() && $task=='save') {
                $configModel = WidgetConfig::findOne([
                    'widget_id' => $widget_id,
                    'context' => $selected_context,
                    'language' => Yii::$app->language
                ]) or $configModel = new WidgetConfig();

                $configModel->loadDefaultValues();
                $configModel->widget_id = $widget_id;
                $configModel->widget_class = $widget_class;
                $configModel->context = $selected_context;
                $configModel->url = $url;
                $configModel->setParamsArray($model->toArray());

                $configModel->save();

                if (Yii::$app->request->getBodyParam('bulk-method')) {
                    foreach (WidgetConfig::find()->where('widget_id=:widget_id AND context>:context AND language=:language', [
                        ':widget_id' => $widget_id,
                        ':context' => $selected_context,
                        ':language' => Yii::$app->language
                    ])->each() as $configModel) {
                        /** @var WidgetConfig $configModel */
                        $configModel->delete();
                    }
                }

                if ($modal) {
                    ModalIFrame::refreshPage();
                } else {
                    return $this->redirect($url);
                }
            }
        }

        if ($modal) {
            Yii::$app->grom->layout = 'modal';
        }

        return $this->render('_formConfig', [
            'model' => $model,
            'widget' => $widget,
            'widget_id' => $widget_id,
            'widget_class' => $widget_class,
            'widget_config' => $widget_config,
            'widget_context' => $widget_context,
            'selected_context' => $selected_context,
            'url' => $url,
        ]);
    }

    /**
     * Finds the WidgetConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WidgetConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WidgetConfig::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
