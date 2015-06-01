<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use gromver\platform\basic\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\basic\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Trash');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php /*// echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('gromver.platform', 'Create {modelClass}', [
    'modelClass' => 'User',
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
                'attribute' => 'id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'attribute' => 'username',
                'vAlign' => GridView::ALIGN_MIDDLE,
            ],
            [
                'attribute' => 'email',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'format' => 'email',
            ],
            [
                'attribute' => 'status',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model) {
                        /** @var User $model */
                        return $model->getStatusLabel();
                    },
                'filter' => User::statusLabels()
            ],
            [
                'attribute' => 'roles',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model) {
                        /** @var User $model */
                        return $model->getIsSuperAdmin() ? '<span class="text-muted">superadmin</span>' : implode(", ", $model->getRoles());
                    },
                'format' => 'html',
                'filter' => \yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
            ],
            'deleted_at:datetime',
            [
                'class' => 'kartik\grid\ActionColumn',
                'width' => '100px',
                'template' => '{view} {restore} {delete}',
                'deleteOptions' => ['data-method' => 'delete'],
                'buttons' => [
                    'restore' => function ($url, $model, $key) {
                        /** @var User $model */
                        return Html::a('<i class="glyphicon glyphicon-open"></i>', ['restore', 'id' => $model->id], [
                            'title' => Yii::t('gromver.platform', 'Restore'),
                            //'data-confirm' => Yii::t('yii', 'Are you sure you want to restore this user?'),
                            'data-method' => 'delete',
                            'data-pjax' => '0'
                        ]);
                    },
                ]
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
            'before' => Html::a('<i class="glyphicon glyphicon-circle-arrow-left"></i> ' . Yii::t('gromver.platform', 'Users'), ['index'], ['class' => 'btn btn-default', 'data-pjax' => 0]),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete'], ['class' => 'btn btn-danger', 'data-pjax'=>'0', 'onclick'=>'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-open"></i> ' . Yii::t('gromver.platform', 'Restore'), ['bulk-restore'], ['class' => 'btn btn-default', 'data-pjax'=>'0', 'onclick'=>'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), ['index-trash'], ['class' => 'btn btn-info']),
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
            alert(<?= json_encode(Yii::t('gromver.platform', 'Выберите элементы.')) ?>)
            return
        }

        $.post($el.attr('href'), {data:selection}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
</script>