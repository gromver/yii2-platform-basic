<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;
use gromver\platform\basic\modules\widget\models\WidgetConfigPersonal;
use Yii;

/**
 * Class WidgetPersonal
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class WidgetPersonal extends Widget {
    /**
     * @inheritdoc
     */
    protected $_configureRoute = '/grom/widget/backend/personal/configure';

    /**
     * @inheritdoc
     */
    protected function findWidgetConfig()
    {
        $parts = empty($this->context) ? [''] : explode('/', '/' . $this->context);

        $contexts = []; $context = '';
        foreach ($parts as $part) {
            $context .= strlen($context) ? '/' . $part : $part;
            $contexts[] = $context;
        }

        return WidgetConfigPersonal::find()->orderBy('context desc')->where(['widget_id' => $this->id, 'language' => Yii::$app->language, 'context' => $contexts, 'created_by' => Yii::$app->user->id])->one();
    }
}