<?php
/**
 * @var $this yii\web\View
 * @var $model string|\gromver\platform\basic\modules\news\models\Category
 */ ?>

<h1 class="page-title title-category"><?=\yii\helpers\Html::encode($model->title)?></h1>

<?= \gromver\platform\basic\widgets\PostList::widget([
    'id' => 'cat-posts',
    'category' => $model,
    'layout' => 'post/listDefault'
]) ?>