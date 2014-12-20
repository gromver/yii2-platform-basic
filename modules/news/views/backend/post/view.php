<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\news\models\Post */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->category->title, 'url' => ['index', 'PostSearch' => ['category_id' => $model->category_id]]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create', 'category_id' => $model->category_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('gromver.platform', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'translation_id',
            'category_id',
            'title',
            'alias',
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
            'ordering',
            'hits',
            'lock',
        ],
    ]) ?>

</div>
