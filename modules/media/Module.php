<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\media;

use gromver\platform\basic\interfaces\DesktopInterface;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements DesktopInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\media\controllers';
    public $defaultRoute = 'backend/default';
    /**
     * @var array elFinder manager controller config
     * @see \mihaildev\elfinder\Controller
     */
    public $elFinderConfig;
    public $desktopOrder = 3;

    public function init()
    {
        parent::init();

        if (!isset($this->elFinderConfig)) {
            $this->elFinderConfig = [
                'access' => ['read'],
                'roots' => [
                    [
                        'baseUrl' => '',
                        'basePath' => '@app/web',
                        'path' => 'files/global',
                        'name' => Yii::t('gromver.platform', 'Global'),
                        'access' => ['write' => 'update']
                    ],
                    [
                        'class' => 'mihaildev\elfinder\UserPath',
                        'baseUrl' => '',
                        'basePath' => '@app/web',
                        'path' => 'files/user_{id}',
                        'name' => Yii::t('gromver.platform', 'My Documents'),
                        'access' => ['read' => 'read', 'write' => 'update']
                    ]
                ]
            ];
        }
        // custom initialization code goes here

        $this->controllerMap = [
            'manager' => array_merge(['class' => 'mihaildev\elfinder\Controller'], $this->elFinderConfig)
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Media'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Media Manager'), 'url' => ['/grom/media/backend/default/index']]
            ]
        ];
    }
}
