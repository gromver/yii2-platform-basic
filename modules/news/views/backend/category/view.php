<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\news\models\Category */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create', 'parentId' => $model->parent_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('gromver.platform', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('gromver.platform', 'Posts'), ['/grom/news/backend/post/index', 'PostSearch' => ['category_id' => $model->id]], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'translation_id',
            'parent_id',
            [
                'attribute' => 'language',
                'value' => \gromver\platform\basic\widgets\Translator::widget(['model' => $model]),
                'format' => 'raw'
            ],
            'title',
            'alias',
            'path',
            'preview_text:ntext',
            'preview_image',
            'detail_text:ntext',
            'detail_image',
            'metakey',
            'metadesc',
            'created_at:datetime',
            'updated_at:datetime',
            'published_at:datetime',
            'status',
            'created_by',
            'updated_by',
            'lft',
            'rgt',
            'level',
            'ordering',
            'hits',
            'lock',
            [
                'attribute' => 'tags',
                'value' => implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', function($tag) {
                    /** @var $tag \gromver\platform\basic\modules\tag\models\Tag */
                    return Html::a($tag->title, $tag->getBackendViewLink());
                })),
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
