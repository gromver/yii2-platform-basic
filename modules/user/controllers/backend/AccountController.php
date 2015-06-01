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
use kartik\widgets\Alert;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class AccountController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\basic\modules\user\Module $module
 */
class AccountController extends \gromver\platform\basic\components\BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'params', 'reset-password'],
                        'roles' => ['authenticated'],
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
        /** @var User $user */
        $model = Yii::$app->user->getIdentity();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionResetPassword()
    {
        /** @var User $model */
        $model = Yii::$app->user->getIdentity();
        $model->scenario = 'resetPassword';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', "Your password has been changed."));
            return $this->redirect(['index']);
        } else {
            return $this->render('reset-password', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionParams()
    {
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();

        $model = $this->extractParamsModel($user);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->setParamsArray($model->toArray());

            if ($user->save()) {
                $this->redirect(['index']);
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
