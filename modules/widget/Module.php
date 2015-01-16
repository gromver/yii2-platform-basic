<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\widget;

use gromver\platform\basic\interfaces\module\DesktopInterface;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements DesktopInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\widget\controllers';
    public $defaultRoute = 'backend/default';
    public $desktopOrder = 10;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Widgets'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Widget\'s Settings'), 'url' => ['/grom/widget/backend/default/index']]
            ]
        ];
    }
}
