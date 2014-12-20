<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use yii\base\Event;

/**
 * Class ActionColumn позволяет изменять кнопки посредством подписки на событие EVENT_CUSTOM_ACTION
 * @see CustomActionEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ActionColumn extends \kartik\grid\ActionColumn
{
    const EVENT_CUSTOM_ACTION = 'customAction';

    public $template = '{custom} {view} {update} {delete}';

    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();

        $this->buttons['custom'] = function($url, $model) {
            $event = new CustomActionEvent([
                'module' => \Yii::$app->controller->getUniqueId(),
                'model' => $model
            ]);
            Event::trigger($this, self::EVENT_CUSTOM_ACTION, $event);

            return implode(' ', $event->buttons);
        };
    }
}