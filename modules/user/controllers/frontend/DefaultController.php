<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user\controllers\frontend;

use kartik\widgets\Alert;
use gromver\models\ObjectModel;
use gromver\platform\basic\modules\user\models\User;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'index'],
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        //todo простую карточку пользователя
        return $this->render('index');
    }

    public function actionUpdate()
    {
        /** @var \gromver\platform\basic\modules\user\models\User $user */
        $user = Yii::$app->user->getIdentity();

        $model = $this->extractParamsModel($user);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->setParamsArray($model->toArray());

            if ($user->save()) {
                Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', "Profile saved."));
                return $this->redirect('');
            } else {
                Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', "It wasn't succeeded to keep the user's parameters. Error:\n{error}", ['error' => implode("\n", $user->getFirstErrors())]));
            }
        }

        return $this->render('update', [
            'user' => $user,
            'model' => $model
        ]);
    }

    /**
     * @param $user User
     * @return ObjectModel | null
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
}
