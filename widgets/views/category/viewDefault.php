<?php
/**
 * @var $this yii\web\View
 * @var $model string|\gromver\platform\basic\news\models\Category
 */
?>

<h1 class="page-title title-category"><?=\yii\helpers\Html::encode($model->title) ?></h1>

<?php echo \gromver\platform\basic\widgets\CategoryList::widget([
    'id' => 'cat-cats',
    'category' => $model,
    'listViewOptions' => [
        'emptyTextOptions' => ['class' => 'hidden']
    ]
]);

echo \gromver\platform\basic\widgets\PostList::widget([
    'id' => 'cat-posts',
    'category' => $model,
]);