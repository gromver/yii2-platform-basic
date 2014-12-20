<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\news\models\Post
 */

use yii\helpers\Html;

if ($this->context->showTranslations) {
    echo \gromver\platform\basic\widgets\Translations::widget([
        'model' => $model,
        'options' => [
            'class' => 'pull-right'
        ]
    ]);
} ?>

<h1 class="page-title title-issue"><?= Html::encode($model->title) ?></h1>

<div class="issue-bar">
    <small class="issue-published"><?= Yii::$app->formatter->asDatetime($model->published_at) ?></small>
    <small class="issue-separator">|</small>
    <?php foreach ($model->tags as $tag) {
        /** @var $tag \gromver\platform\basic\tag\models\Tag */
        echo Html::a($tag->title, ['/grom/tag/default/posts', 'tag_id' => $tag->id, 'tag_alias' => $tag->alias, 'category_id' => $model->category_id], ['class' => 'issue-tag badge']);
    } ?>
</div>

<?php if($model->detail_image) {
    echo Html::img($model->getFileUrl('detail_image'), [
        'class' => 'text-block img-responsive',
        //'style' => 'max-width: 200px; margin-right: 15px;'
    ]);
} ?>

<div class="issue-text"><?= $model->detail_text ?></div>

<style>
    .issue-bar {
        border-top: 1px solid #cccccc;
        padding: 6px;
        margin-top: 0.5em;
        margin-bottom: 1.8em;
        background-color: #f5f5f5;
        font-size: 12px;
    }
    .issue-separator {
        margin: 0 8px;
    }
    .issue-separator:last-child {
        display: none;
    }
    .issue-tag {
        font-size: 10px;
        margin-right: 0.8em;
    }
</style>