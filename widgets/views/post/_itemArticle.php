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

$urlManager = Yii::$app->urlManager; ?>

<h4 class="article-title<?= $model->postViewed ? ' viewed' : (Yii::$app->user->isGuest ? '' : ' new') ?>">
    <?= Html::a(Html::encode($model->title), $model->getFrontendViewLink()) ?>
</h4>
<?php if($model->preview_image) echo Html::img($model->getFileUrl('preview_image'), [
    'class' => 'pull-left',
    'style' => 'max-width: 200px; margin-right: 15px;'
]); ?>

<div class="article-preview">
    <?= $model->preview_text ?>
</div>

<div class="clearfix"></div>
