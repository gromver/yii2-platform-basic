<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\media;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\modules\main\widgets\Desktop;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\media\controllers';
    public $defaultRoute = 'backend/default';
    /**
     * @var array elFinder manager controller config
     * @see \mihaildev\elfinder\Controller
     */
    public $elFinderConfig;

    public function init()
    {
        parent::init();

        if (!isset($this->elFinderConfig)) {
            $this->elFinderConfig = [
                'access' => ['readMedia'],
                'roots' => [
                    [
                        'baseUrl' => '',
                        'basePath' => '@app/web',
                        'path' => 'files/global',
                        'name' => Yii::t('gromver.platform', 'Global'),
                        'access' => ['read' => 'readMedia', 'write' => 'updateMedia']
                    ],
                    [
                        'class' => 'mihaildev\elfinder\UserPath',
                        'baseUrl' => '',
                        'basePath' => '@app/web',
                        'path' => 'files/user_{id}',
                        'name' => Yii::t('gromver.platform', 'My Documents'),
                        'access' => ['write' => 'updateMedia']
                    ]
                ]
            ];
        }

        $this->controllerMap = [
            'manager' => array_merge(['class' => 'mihaildev\elfinder\Controller'], $this->elFinderConfig)
        ];
    }

    /**
     * @param $event \gromver\platform\basic\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Media'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Media Manager'), 'url' => ['/grom/media/backend/default/index']]
            ]
        ];
    }

    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem'
        ];
    }
}
