<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\platform\basic\modules\auth\models\LoginForm;

/**
 * Class AuthLogin
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class AuthLogin extends Widget
{
    /**
     * @ignore
     * @var LoginForm
     */
    public $model;

    public function init()
    {
        parent::init();

        $this->setConfigureAccess('none');

        if (!isset($this->model)) {
            $this->model = new LoginForm();
        }
    }

    protected function launch()
    {
        echo $this->render('auth/login', [
            'model' => $this->model
        ]);
    }
}