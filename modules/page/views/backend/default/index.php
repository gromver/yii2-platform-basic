<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\basic\page\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php /*// echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('gromver.platform', 'Create {modelClass}', [
    'modelClass' => 'Page',
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
            ['attribute' => 'id', 'width' => '50px'],
            [
                'attribute' => 'language',
                'width' => '80px',
                'value' => function($model) {
                        /** @var $model \gromver\platform\basic\page\models\Page */
                        return \gromver\platform\basic\widgets\Translator::widget(['model' => $model]);
                    },
                'format' => 'raw',
                'filter' => Yii::$app->getLanguagesList()
            ],
            'title',
            'alias',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                        /** @var $model \gromver\platform\basic\page\models\Page */
                        return $model->status === \gromver\platform\basic\page\models\Page::STATUS_PUBLISHED ? Html::a('<i class="glyphicon glyphicon-ok-circle"></i>', \yii\helpers\Url::to(['unpublish', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0', 'data-method' => 'post']) : Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', \yii\helpers\Url::to(['publish', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0', 'data-method' => 'post']);
                    },
                'filter' => \gromver\platform\basic\news\models\Post::statusLabels(),
                'format' => 'raw',
                'width'=>'80px'
            ],
            [
                'attribute' => 'tags',
                'value' => function($model){
                        /** @var $model \gromver\platform\basic\page\models\Page */
                        return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                    },
                'filterType' => \dosamigos\selectize\Selectize::className(),
                'filterWidgetOptions' => [
                    'items' => \yii\helpers\ArrayHelper::map(\gromver\platform\basic\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title', 'group'),
                    'clientOptions' => [
                        'maxItems' => 1
                    ],
                    'url' => ['/grom/tag/default/tag-list']
                ]
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'deleteOptions' => ['data-method' => 'delete']
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . '</h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete'], ['class' => 'btn btn-danger', 'data-pjax' => '0', 'onclick' => 'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]) ?>

</div>
<script>
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