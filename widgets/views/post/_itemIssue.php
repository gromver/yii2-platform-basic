<?php
/**
 * @var $this yii\web\View
 * @var $model Post
 * @var $key string
 * @var $index integer
 * @var $widget \yii\widgets\ListView
 * @var $postListWidget \gromver\platform\basic\widgets\PostList
 */

use yii\helpers\Html;
use gromver\platform\basic\modules\news\models\Post;

$urlManager = Yii::$app->urlManager; ?>
<div class="issue-wrapper">
    <h4 class="issue-title<?= $model->postViewed ? ' viewed' : (Yii::$app->user->isGuest ? '' : ' new') ?>"><?= Html::a(Html::encode($model->title), $model->getFrontendViewLink()) ?></h4>
    <div class="issue-bar">
        <small class="issue-published"><?= Yii::$app->formatter->asDatetime($model->published_at) ?></small>
        <small class="issue-separator">|</small>
        <?php foreach ($model->tags as $tag) {
            /** @var $tag \gromver\platform\basic\modules\tag\models\Tag */
            echo Html::a($tag->title, ['/grom/tag/default/posts', 'tag_id' => $tag->id, 'tag_alias' => $tag->alias, 'category_id' => $postListWidget->category ? $postListWidget->category->id : null], ['class' => 'issue-tag badge']);
        } ?>
    </div>
    <?php if($model->preview_image) {
        echo Html::img($model->getFileUrl('preview_image'), [
            'class' => 'pull-left',
            'style' => 'max-width: 200px; margin-right: 15px;'
        ]);
    } ?>

    <div class="issue-preview"><?= $model->preview_text ? $model->preview_text : \yii\helpers\StringHelper::truncateWords(strip_tags($model->detail_text), 50) ?></div>
    <div class="clearfix"></div>
</div>