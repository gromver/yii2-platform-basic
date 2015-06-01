<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user\controllers\backend;


use gromver\models\ObjectModel;
use gromver\platform\basic\modules\user\models\User;
use gromver\platform\basic\modules\user\models\UserSearch;
use kartik\widgets\Alert;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class DefaultController implements the CRUD actions for User model.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\basic\modules\user\Module $module
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
                    'trash' => ['post', 'delete'],
                    'bulk-trash' => ['post'],
                    'restore' => ['post', 'delete'],
                    'bulk-restore' => ['post'],
                    'login-as' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['readUser'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createUser'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'params'],
                        'roles' => ['updateUser'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'bulk-delete'],
                        'roles' => ['deleteUser'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['trash', 'bulk-trash', 'index-trash', 'restore', 'bulk-restore'],
                        'roles' => ['trashUser'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login-as'],
                        'roles' => ['loginAsUser'],
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
     * Lists all trashed User models.
     * @return mixed
     */
    public function actionIndexTrash()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('indexTrash', [
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

        // проверка на суперадминство
        if ($model->getIsSuperAdmin() && $model->id != Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на право админить данного пользователя
        if (!Yii::$app->user->can('administrateUser', ['user' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
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

        // нельзя удалять свой аккаунт
        if ($model->id == Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на суперадминство
        if ($model->getIsSuperAdmin()) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на право админить данного пользователя
        if (!Yii::$app->user->can('administrateUser', ['user' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->getIsTrashed() && $this->module->allowDelete) {
            $model->delete();
        }

        if(Yii::$app->request->getIsDelete()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
        }

        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionBulkDelete()
    {
        if (!$this->module->allowDelete) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $data = Yii::$app->request->getBodyParam('data', []);

        $models = User::find()->trashed()->andWhere(['id' => $data])->all();

        foreach ($models as $model) {
            /** @var $model User */
            if (!$model->getIsSuperAdmin() && $model->id != Yii::$app->user->id && Yii::$app->user->can('administrateUser', ['user' => $model])) {
                // удаляем если пользователь не суперадмин, не является текущим пользователем и если текущий пользователь имеет право удалять
                $model->delete();
            }
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @param string|null $backUrl
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionTrash($id, $backUrl = null)
    {
        $model = $this->findModel($id);

        // нельзя удалять свой аккаунт
        if ($model->id == Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на суперадминство
        if ($model->getIsSuperAdmin()) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на право админить данного пользователя
        if (!Yii::$app->user->can('administrateUser', ['user' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if (!$model->getIsTrashed()) {
            $model->trash();
        }

        if (Yii::$app->request->getIsDelete()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
        }

        return $this->redirect($backUrl ? $backUrl : ['index']);
    }

    /**
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionBulkTrash()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = User::find()->published()->andWhere(['id' => $data])->all();

        foreach ($models as $model) {
            /** @var $model User */
            if (!$model->getIsSuperAdmin() && $model->id != Yii::$app->user->id && Yii::$app->user->can('administrateUser', ['user' => $model])) {
                $model->trash();
            }
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionRestore($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('administrateUser', ['user' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->getIsTrashed()) {
            $model->untrash();
        }

        if (Yii::$app->request->getIsDelete()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
        }

        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionBulkRestore()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = User::find()->trashed()->andWhere(['id' => $data])->all();

        foreach ($models as $model) {
            $model->untrash();
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

        // нельзя удалять свой аккаунт
        if ($user->id == Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на суперадминство
        if ($user->getIsSuperAdmin()) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на право админить данного пользователя
        if (!Yii::$app->user->can('administrateUser', ['user' => $user])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
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

    public function actionLoginAs($id)
    {
        $model = $this->findModel($id);

        // проверка на суперадминство
        if ($model->getIsSuperAdmin()) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        // проверка на право админить данного пользователя
        if (!Yii::$app->user->can('administrateUser', ['user' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->login(3600);
        $this->redirect(['view', 'id' => $model->id]);
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
