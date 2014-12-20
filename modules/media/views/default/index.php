<?php
/**
 * @var $this \yii\web\view
 */

use yii\helpers\Html;

$this->title = Yii::t('gromver.platform', 'Media Manager');
$this->params['breadcrumbs'][] = $this->title;

\yii\jui\Resizable::begin([
    'options' => ['style' => 'height: 400px;'],
    'clientOptions' => ['handles' => 's']
]);

echo Html::tag('iframe', '', [
    'id' => 'elFinder',
    'src' => \yii\helpers\Url::toRoute(['manager/manager']),
    'width' => '100%',
    'height' => '100%',
    'style' => 'border: none;'
]);

\yii\jui\Resizable::end();