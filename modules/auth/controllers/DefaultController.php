<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\auth\controllers;

use gromver\platform\basic\modules\auth\models\LoginForm;
use gromver\platform\basic\modules\user\models\User;
use gromver\widgets\ModalIFrame;
use kartik\widgets\Alert;
use Yii;
use yii\di\Instance;
use yii\filters\AccessControl;
use yii\mail\BaseMailer;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\basic\modules\auth\Module Module
 */
class DefaultController extends Controller
{
    public $mailer = 'mailer';

    private $loginAttemptsVar = '__LoginAttemptsCount';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin($modal = null)
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = new LoginForm();

        //make the captcha required if the unsuccessful attempts are more of thee
        if ($this->getLoginAttempts() >= $this->module->attemptsBeforeCaptcha) {
            $model->scenario = 'withCaptcha';
        }

        if ($model->load($_POST)) {
            if($model->login()) {
                $this->setLoginAttempts(0); //if login is successful, reset the attempts
                if ($modal) {
                    ModalIFrame::refreshPage();
                }
                return $this->goBack();
            } else {
                //if login is not successful, increase the attempts
                $this->setLoginAttempts($this->getLoginAttempts() + 1);
                Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', 'Authorization is failed.'));
            }
        }

        if ($modal) {
            Yii::$app->grom->applyModalLayout();
        } else {
            $this->module->layout = $this->module->loginLayout;
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    private function getLoginAttempts()
    {
        return Yii::$app->getSession()->get($this->loginAttemptsVar, 0);
    }

    private function setLoginAttempts($value)
    {
        Yii::$app->getSession()->set($this->loginAttemptsVar, $value);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new User();
        $model->setScenario('signup');
        if ($model->load($_POST) && $model->save()) {
            if (Yii::$app->getUser()->login($model)) {
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new User();
        $model->scenario = 'requestPasswordResetToken';
        if ($model->load($_POST) && $model->validate()) {
            if ($this->sendPasswordResetEmail($model->email)) {
                Yii::$app->getSession()->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Check your email for further instructions.'));
                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', 'There was an error sending email.'));
            }
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $model = User::findOne([
            'password_reset_token' => $token,
            'status' => User::STATUS_ACTIVE,
        ]);

        if (!$model) {
            throw new BadRequestHttpException(Yii::t('gromver.platform', 'Wrong password reset token.'));
        }

        $model->scenario = 'resetPassword';
        if ($model->load($_POST) && $model->save()) {
            Yii::$app->getSession()->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'New password was saved.'));
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    private function sendPasswordResetEmail($email)
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $email,
        ]);

        if (!$user) {
            return false;
        }

        $user->password_reset_token = Yii::$app->security->generateRandomString();
        if ($user->save(false)) {
            $mailer = Instance::ensure($this->mailer, BaseMailer::className());

            return $mailer->compose('@gromver/platform/basic/modules/auth/views/emails/passwordResetToken', ['user' => $user])
                ->setFrom(Yii::$app->grom->params['supportEmail'], Yii::t('gromver.platform', '{name} robot', ['name' => Yii::$app->grom->siteName]))
                ->setTo($user->email)
                ->setSubject(Yii::t('gromver.platform', 'Password reset for {name}.', ['name' => Yii::$app->grom->siteName]))
                ->send();
        }

        return false;
    }
}
