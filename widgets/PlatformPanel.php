<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


/**
 * Class PlatformPanel
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PlatformPanel extends \yii\bootstrap\Widget
{
    public $layout = 'platform/panelFrontend';
    public $searchRoute = '/grom/sqlsearch/frontend/default/index';

    public function run()
    {
        return $this->render($this->layout, [
            'widget' => $this
        ]);
    }
}