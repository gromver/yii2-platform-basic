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
 * Class WidgetPersonal
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class WidgetPersonal extends Widget {
    protected function preInit()
    {
        if (!isset($this->context)) {
            $this->context = \Yii::$app->request->getPathInfo();
        }

        if (!\Yii::$app->user->getIsGuest()) {
            $this->context .= '/u' . \Yii::$app->user->id;
        }

        parent::preInit();
    }
} 