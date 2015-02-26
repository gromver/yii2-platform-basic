<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use gromver\platform\basic\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\basic\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Users');
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
            ['class' => 'yii\grid\CheckboxColumn'],

            [
                'attribute' => 'id',
                'width' => '50px'
            ],
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function($model) {
                        /** @var User $model */
                        return $model->getStatusLabel();
                    },
                'filter' => ['' => 'Не выбрано'] + User::statusLabels()
            ],
            // 'created_at',
            // 'updated_at',
            // 'deleted_at',
            // 'last_visit_at',
            [
                'attribute' => 'roles',
                'value' => function($model) {
                        /** @var User $model */
                        return implode(", ", $model->getRoles());
                    },
                'filter' => \yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'width' => '100px',
                //'class' => 'gromver\platform\basic\widgets\ActionColumn',
                'template' => '{login} {params} {view} {update} {delete}',
                'deleteOptions' => ['data-method'=>'delete'],
                'buttons' => [
                    'params' => function ($url, $model, $key) {
                            /** @var User $model */
                            return Html::a('<i class="glyphicon glyphicon-user"></i>', ['params', 'id' => $model->id], ['title' => Yii::t('gromver.platform', 'User params')]);
                        },
                    'login' => function ($url, $model, $key) {
                        /** @var User $model */
                        return Yii::$app->user->can('administrate') ? Html::a('<i class="glyphicon glyphicon-log-in"></i>', ['login-as', 'id' => $model->id], ['title' => Yii::t('gromver.platform', 'Login as {user}', ['user' => $model->username]), 'data-method' => 'post', 'data-confirm' => Yii::t('platform.gromver', 'Are you sure want to login as {user}?', ['user' => $model->username])]) : '';
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
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => 0]),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete'], ['class' => 'btn btn-danger', 'data-pjax'=>'0', 'onclick'=>'processAction(this); return false']) . ' ' .
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
            alert(<?= json_encode(Yii::t('gromver.platform', 'Выберите элементы.')) ?>)
            return
        }

        $.post($el.attr('href'), {data:selection}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
</script>