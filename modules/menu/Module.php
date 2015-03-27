<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\menu;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\widgets\Desktop;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\menu\controllers';
    public $defaultRoute = 'backend/item';
    public $desktopOrder = 4;

    /**
     * @param $event \gromver\platform\basic\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Menu'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['/grom/menu/backend/type/index']],
                ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['/grom/menu/backend/item/index']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem'
        ];
    }
}
