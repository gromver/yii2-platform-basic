<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\backend\modules\user\controllers;

use kartik\widgets\Alert;
use gromver\models\ObjectModel;
use gromver\platform\basic\user\models\User;
use gromver\platform\basic\user\models\UserSearch;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class DefaultController implements the CRUD actions for User model.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\basic\backend\modules\user\Module $module
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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'bulk-delete'],
                        'matchCallback' => function() {
                                return Yii::$app->user->isSuperAdmin;
                            }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'params'],
                        'roles' => ['update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';
        $model->status = User::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!Yii::$app->user->isSuperAdmin && $model->id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('gromver.platform', 'You can edit only your profile.'));
        }

        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!Yii::$app->user->isSuperAdmin && $model->id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('gromver.platform', 'You can remove only your profile.'));
        }

        if ($model->status !== User::STATUS_DELETED) {
            $model->safeDelete();
        } elseif ($this->module->allowDelete) {
            $model->delete();
        }

        if(Yii::$app->request->getIsDelete())
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));

        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionBulkDelete()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = User::findAll(['id'=>$data]);

        foreach ($models as $model) {
            if ($model->status !== User::STATUS_DELETED) {
                $model->safeDelete();
            } elseif ($this->module->allowDelete) {
                $model->delete();
            }
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param $id integer User id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionParams($id)
    {
        $user = $this->findModel($id);
        if (!Yii::$app->user->isSuperAdmin && $user->id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('gromver.platform', 'You can edit only your profile.'));
        }

        $model = $this->extractParamsModel($user);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->setParamsArray($model->toArray());

            if ($user->save()) {
                $this->redirect(['view', 'id' => $user->id]);
            } else {
                Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', "It wasn't succeeded to keep the user's parameters. Error:\n{error}", ['error' => implode("\n", $user->getFirstErrors())]));
            }
        }

        return $this->render('params', [
            'user' => $user,
            'model' => $model
        ]);
    }

    /**
     * @param $user User
     * @return ObjectModel
     */
    protected function extractParamsModel($user)
    {
        if ($this->module->userParamsClass) {
            try {
                $attributes = $user->getParamsArray();
            } catch(InvalidParamException $e) {
                $attributes = [];
            }

            $model = new ObjectModel($this->module->userParamsClass);
            $model->setAttributes($attributes);

            return $model;
        }
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
