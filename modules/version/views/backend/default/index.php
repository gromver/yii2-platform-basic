<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\basic\modules\version\models\VersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Version Manager');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php /*// echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('gromver.platform', 'Create {modelClass}', [
    'modelClass' => 'Version',
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
            ['class' => '\kartik\grid\CheckboxColumn'],
            [
                'attribute'=>'id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width'=>'40px'
            ],
            [
                'attribute'=>'item_id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width'=>'40px'
            ],
            [
                'attribute' => 'item_class',
                'vAlign' => GridView::ALIGN_MIDDLE,
            ],
            [
                'attribute' => 'version_note',
                'vAlign' => GridView::ALIGN_MIDDLE,
            ],
            [
                'attribute' => 'created_at',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'format' => ['date', 'd MMM Y H:mm'],
                'width' => '160px',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'options' => ['value' => is_int($searchModel->created_at) ? date('d.m.Y', $searchModel->created_at) : ''],
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy'
                    ]
                ]
            ],
            [
                'attribute' => 'created_by',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model) {
                        /** $model \gromver\platform\basic\modules\version\models\Version */
                        return $model->user ? $model->user->username : $model->created_by;
                    }
            ],
            [
                'attribute' => 'keep_forever',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value'=>function($model) {
                        /** $model \gromver\platform\basic\modules\version\models\Version */
                        return Html::a($model->keep_forever ? Yii::t('gromver.platform', 'Yes') . ' <small class="glyphicon glyphicon-lock"></small>' : Yii::t('gromver.platform', 'No'), ['keep-forever', 'id' => $model->id], [
                            'class'=>'btn btn-xs btn-default active btn-keep-forever',
                            'data-method' => 'post',
                            'data-pjax' => 0
                        ]);
                    },
                'width'=>'80px',
                'format' => 'raw'
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'deleteOptions' => ['data-method' => 'delete'],
                'buttons' => [
                    'restore' => function($url) {
                            return Html::a('<span class="glyphicon glyphicon-open"></span>', $url, [
                                'title' => Yii::t('gromver.platform', 'Restore'),
                                'data-method' => 'post',
                                'data-pjax' => 0
                            ]);
                        },
                ],
                'template'=>'{restore} {view} {update} {delete}'
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type' => 'info',
            'before' => ' ',
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
