<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\auth;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\modules\main\widgets\Desktop;
use gromver\platform\basic\modules\menu\widgets\MenuItemRoutes;
use Yii;

/**
 * Class Module
 * Этот модуль используется админкой для авторизации пользователя, можно настроить период запоминания пользователя в куках,
 * количесвто безуспешных попыток авторизации с последущим подключением капчи
 *
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\auth\controllers';
    /**
     * @var int
     * @desc Remember Me Time (seconds), default = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days
    public $attemptsBeforeCaptcha = 3; // Unsuccessful Login Attempts before Captcha
    public $authLayout = 'auth';    // если null то применится макет приложения

    public function init()
    {
        if ($this->authLayout) {
            $this->layout = $this->authLayout;
        }
    }

    /**
     * @param $event \gromver\platform\basic\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
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
     * @param $event \gromver\platform\basic\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Auth'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Login'), 'url' => ['/grom/auth/default/login']],
                ['label' => Yii::t('gromver.platform', 'Password Reset'), 'url' => ['/grom/auth/default/request-password-reset']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes'
        ];
    }
}
