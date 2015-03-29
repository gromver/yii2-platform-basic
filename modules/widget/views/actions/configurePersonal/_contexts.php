<?php

use yii\helpers\Html;
use gromver\platform\basic\modules\widget\models\WidgetConfigPersonal;

/**
 * @var yii\web\View $this
 * @var string $widget_id
 * @var string $widget_context
 * @var string $selected_context
 * @var string $loaded_context
 */

$contexts = empty($widget_context) ? [''] : explode('/', '/' . $widget_context);
$context = '';
foreach ($contexts as $i => $part) {
    $context .= strlen($context) ? '/'.$part : $part;
    $class = 'btn btn-link ';
    $class .= WidgetConfigPersonal::find()->where(['widget_id' => $widget_id, 'context' => $context])->exists() ? 'defined' : 'undefined';
    if ($loaded_context == $context) {
        $class .= ' loaded';
    }
    if ($selected_context == $context) {
        $class .= ' selected';
    }
    $description = empty($part) ? Yii::t('gromver.platform', 'Default') : $part;


    if ($i) {
        echo Html::tag('span', ' / ', ['class' => 'separator']);
    }
    echo Html::submitButton($description, ['class' => $class, 'name' => 'selected_context', 'value' => $context]);
}