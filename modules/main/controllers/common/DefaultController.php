<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-grom/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\controllers\common;


use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \yii\web\Controller
{
    /**
     * Можно менять настройки капчи через DI
     * @var array
     */
    public $captchaActionConfig = [
        'class' => 'yii\captcha\CaptchaAction'
    ];

    /**
     * Можно менять настройки страницы ошибок через DI
     * @var array
     */
    public $errorActionConfig = [
        'class' => 'yii\web\ErrorAction'
    ];

    public function actions()
    {
        return [
            'error' => $this->errorActionConfig,
            'captcha' => $this->captchaActionConfig
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'error') {
            Yii::$app->grom->applyErrorLayout();
        }

        return parent::beforeAction($action);
    }
}