<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\news\models\Post
 * @var $key string
 * @var $index integer
 * @var $widget \yii\widgets\ListView
 * @var $postListWidget \gromver\platform\basic\widgets\PostList
 */

use yii\helpers\Html;
use backend\modules\news\models\Post;

$urlManager = Yii::$app->urlManager;

echo '<h4>' . Html::a(Html::encode($model->title), $urlManager->createUrl($model->getFrontendViewLink())) . '</h4>';

if($model->preview_image) echo Html::img($model->getFileUrl('preview_image'), [
    'class' => 'pull-left',
    'style' => 'max-width: 200px; margin-right: 15px;'
]);

echo Html::tag('div', $model->preview_text);

echo '<div class="clearfix"></div>';
