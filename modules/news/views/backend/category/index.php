<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\basic\modules\news\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Категории');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php /*<p>
        <?= Html::a(Yii::t('gromver.platform', 'Create {modelClass}', [
    'modelClass' => 'Category',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>*/?>

    <?= GridView::widget([
        'id' => 'table-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute' => 'id',
                'width' => '50px'
            ],
            [
                'attribute' => 'language',
                'width' => '80px',
                'value' => function($model) {
                        /** @var \gromver\platform\basic\modules\news\models\Category $model */
                        return \gromver\platform\basic\widgets\Translator::widget(['model' => $model]);
                    },
                'format' => 'raw',
                'filter' => Yii::$app->getLanguagesList()
            ],
            [
                'attribute' => 'title',
                'value' => function($model){
                        /** @var \gromver\platform\basic\modules\news\models\Category $model */
                        return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', ' — ' . $model->path, ['class' => 'text-muted']);
                    },
                'format' => 'html'
            ],
            //'alias',
            //'path',
            [
                'attribute' => 'tags',
                'value' => function($model){
                        /** @var \gromver\platform\basic\modules\news\models\Category $model */
                        return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                    },
                'filterType' => \dosamigos\selectize\Selectize::className(),
                'filterWidgetOptions' => [
                    'items' => \yii\helpers\ArrayHelper::map(\gromver\platform\basic\modules\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title', 'group'),
                    'clientOptions' => [
                        'maxItems' => 1
                    ],
                    'url' => ['/grom/tag/default/tag-list']
                ]
            ],
            [
                'header' => Yii::t('gromver.platform', 'Posts'),
                'value' => function($model) {
                        /** @var \gromver\platform\basic\modules\news\models\Category $model */
                        return Html::a('('.$model->getPosts()->count().')', ['post/index', 'PostSearch[category_id]' => $model->id], ['data-pjax' => 0]);
                    },
                'format'=>'raw'
            ],
            [
                'attribute' => 'published_at',
                'format' => ['date', 'd MMM Y H:mm'],
                'width' => '160px',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'options' => ['value' => is_int($searchModel->published_at) ? date('d.m.Y', $searchModel->published_at) : ''],
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy'
                    ]
                ]
            ],
            [
                'attribute' => 'status',
                'value' => function ($model, $index, $widget) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Category */
                        return $model->status === \gromver\platform\basic\modules\news\models\Category::STATUS_PUBLISHED ? Html::a('<i class="glyphicon glyphicon-ok-circle"></i>', \yii\helpers\Url::to(['unpublish', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0', 'data-method' => 'post']) : Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', \yii\helpers\Url::to(['publish', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0', 'data-method' => 'post']);
                    },
                'filter' => \gromver\platform\basic\modules\news\models\Category::statusLabels(),
                'format' => 'raw',
                'width' => '80px'
            ],
            [
                'attribute' => 'ordering',
                'value' => function($model, $index) {
                        /** @var \gromver\platform\basic\modules\news\models\Category $model */
                        return Html::input('text', 'order', $model->ordering, ['class'=>'form-control']);
                    },
                'format' => 'raw',
                'width' => '100px'
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'deleteOptions' => ['data-method'=>'delete']
            ],
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '. Html::encode($this->title) . ' </h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-sort-by-attributes"></i> ' . Yii::t('gromver.platform', 'Ordering'), ['ordering'], ['class' => 'btn btn-default', 'data-pjax' => '0', 'onclick' => 'processOrdering(this); return false']).' '.
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete'], ['class' => 'btn btn-danger', 'data-pjax' => '0', 'onclick' => 'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]) ?>

</div>

<script>
    function processOrdering(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows'),
            data = {}
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }
        $.each(selection, function(index, value){
            data[value] = $grid.find('tr[data-key="'+value+'"] input[name="order"]').val()
        })

        $.post($el.attr('href'), {data:data}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
    function processAction(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows')
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }

        $.post($el.attr('href'), {data:selection}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
</script>