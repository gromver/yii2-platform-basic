<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 07.03.15
 * Time: 12:29
 */

namespace gromver\platform\basic\widgets;


use yii\base\ErrorException;

class WidgetPersonal extends Widget {
    protected function preInit()
    {
        if (\Yii::$app->user->getIsGuest()) {
            throw new ErrorException('You must be authorized.');
        }

        if (!isset($this->context)) {
            $this->context = \Yii::$app->request->getPathInfo();
        }

        $this->context .= '/u' . \Yii::$app->user->id;

        parent::preInit();
    }
} 