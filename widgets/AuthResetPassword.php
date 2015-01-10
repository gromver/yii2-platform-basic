<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use gromver\platform\basic\modules\user\models\User;

/**
 * Class AuthResetPassword
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class AuthResetPassword extends Widget {
    /**
     * @ignore
     * @var User
     */
    public $model;

    public function init()
    {
        parent::init();

        $this->setConfigureAccess('none');

        if (!isset($this->model)) {
            $this->model = new User();
        }
    }

    protected function launch()
    {
        echo $this->render('auth/resetPassword', [
            'model' => $this->model
        ]);
    }
}