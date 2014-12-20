<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\news\models\Category
 * @var $key string
 * @var $index integer
 * @var $widget \yii\widgets\ListView
 */

use yii\helpers\Html;
use backend\modules\news\models\Category;

echo '<h3>' . Html::a(Html::encode($model->title), $model->getViewLink()) . '</h3>';

if($model->preview_image) echo Html::img($model->getFileUrl('preview_image'), [
    'class' => 'pull-left',
    'style' => 'max-width: 200px; margin-right: 15px;'
]);

echo Html::tag('div', $model->preview_text);

echo '<div class="clearfix"></div>';
