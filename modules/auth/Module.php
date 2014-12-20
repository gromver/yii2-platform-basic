<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\auth;

use gromver\platform\basic\interfaces\DesktopInterface;
use gromver\platform\basic\interfaces\MenuItemRoutesInterface;
use Yii;

/**
 * Class Module
 * Этот модуль используется админкой для авторизации пользователя, можно настроить период запоминания пользователя в куках,
 * количесвто безуспешных попыток авторизации с последущим подключением капчи
 *
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements MenuItemRoutesInterface, DesktopInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\auth\controllers';
    /**
     * @var int
     * @desc Remember Me Time (seconds), default = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days
    public $attemptsBeforeCaptcha = 3; // Unsuccessful Login Attempts before Captcha
    public $loginLayout = 'login';
    public $desktopOrder = 2;

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Auth'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Login'), 'route' => 'grom/auth/default/login'],
                ['label' => Yii::t('gromver.platform', 'Signup'), 'route' => 'grom/auth/default/signup'],
                ['label' => Yii::t('gromver.platform', 'Request password reset token'), 'route' => 'grom/auth/default/request-password-reset-token'],
                ['label' => Yii::t('gromver.platform', 'Reset password'), 'route' => 'grom/auth/default/reset-password'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Auth'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Login'), 'url' => ['/grom/auth/default/login']],
                ['label' => Yii::t('gromver.platform', 'Password Reset'), 'url' => ['/grom/auth/default/request-password-reset']],
            ]
        ];
    }
}
