<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\version;


use gromver\platform\basic\interfaces\module\DesktopInterface;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements DesktopInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\version\controllers';
    public $defaultRoute = 'backend/default';
    public $desktopOrder = 9;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Versions'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Versions'), 'url' => ['/grom/version/backend/default/index']]
            ]
        ];
    }
}
