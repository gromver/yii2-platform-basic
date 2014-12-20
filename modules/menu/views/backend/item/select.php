<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var gromver\platform\basic\modules\menu\models\MenuItemSearch $searchModel
 */

$this->title = Yii::t('gromver.platform', 'Select Menu Item');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

	<?/*<h1><?= Html::encode($this->title) ?></h1>*/?>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'id' => 'grid',
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'width' => '50px'
            ],
            [
                'attribute' => 'language',
                'width' => '50px',
                'filter' => Yii::$app->getLanguagesList()
            ],
            [
                'attribute' => 'title',
                'value' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\menu\models\MenuItem */
                        return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', $model->path, ['class' => 'text-muted']);
                    },
                'format' => 'html'

            ],
			[
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \gromver\platform\basic\modules\menu\models\MenuItem */
                    return $model->getStatusLabel();
                },
                'filter' => \gromver\platform\basic\modules\menu\models\MenuItem::statusLabels()
            ],
            [
                'value' => function ($model) {
                        return Html::a(Yii::t('gromver.platform', 'Select'), '#', [
                            'class' => 'btn btn-primary btn-xs',
                            'onclick' => \gromver\widgets\ModalIFrame::emitDataJs([
                                    'id' => $model->id,
                                    'description' => Yii::t('gromver.platform', 'Menu Item: {title}', ['title' => $model->title]),
                                    'link' => Yii::$app->urlManager->createUrl($model->viewLink),
                                    'value' => $model->id . ':' . $model->alias
                                ]),
                        ]);
                    },
                'format'=>'raw'
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 0],
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . '</h3>',
            'type' => 'info',
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), [null], ['class' => 'btn btn-info']),
            'showFooter' => false,
        ],
	]) ?>

</div>