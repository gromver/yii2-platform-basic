<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use kartik\widgets\Alert;
use gromver\platform\basic\modules\main\models\ContactForm;
use Yii;

/**
 * Class Contact
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Contact extends Widget
{
    /**
     * @type yesno
     * @translation gromver.platform
     */
    public $withCaptcha;
    /**
     * @translation gromver.platform
     */
    public $layout = 'contact/form';
    /**
     * @translation gromver.platform
     */
    public $successLayout = 'contact/success';
    // todo параметр емейла на который отсылать письма

    protected function launch()
    {
        $model = new ContactForm();
        if ($this->withCaptcha) {
            $model->scenario = 'withCaptcha';
        }

        if (!Yii::$app->user->isGuest) {
            /** @var \gromver\platform\basic\modules\user\models\User $user */
            $user = Yii::$app->user->identity;
            $userParams = $user->getParamsArray();
            $model->name = $userParams['name'] ? $userParams['name'] : $user->username;
            $model->email = $user->email;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->grom->params['adminEmail'])) {
                Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Email is sent.'));
                return $this->render($this->successLayout);
            } else {
                Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', 'There was an error.'));
            }
        }

        echo $this->render($this->layout, [
            'model' => $model
        ]);
    }
}