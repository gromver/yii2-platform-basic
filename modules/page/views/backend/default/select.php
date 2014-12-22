<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var gromver\platform\basic\modules\page\models\PageSearch $searchModel
 * @var string $route
 */

$this->title = Yii::t('gromver.platform', 'Select Page');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

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
                    /** @var $model \gromver\platform\basic\modules\page\models\Page */
                    return $model->title . '<br/>' . Html::tag('small', $model->alias, ['class' => 'text-muted']);
                },
                'format' => 'html'

            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \gromver\platform\basic\modules\page\models\Page */
                    return $model->getStatusLabel();
                },
                'filter' => \gromver\platform\basic\modules\page\models\Page::statusLabels()
            ],
            [
                'attribute' => 'tags',
                'value' => function ($model){
                    /** @var $model \gromver\platform\basic\modules\page\models\Page */
                    return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                },
                'filterType' => \dosamigos\selectize\Selectize::className(),
                'filterWidgetOptions' => [
                    'items' => \yii\helpers\ArrayHelper::map(\gromver\platform\basic\modules\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title', 'group'),
                    'clientOptions' => [
                        'maxItems' => 1
                    ],
                    'url' => ['/grom/default/tag-list']
                ]
            ],
            [
                'value' => function ($model) use ($route) {
                    /** @var $model \gromver\platform\basic\modules\page\models\Page */
                    return Html::a(Yii::t('gromver.platform', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \gromver\widgets\ModalIFrame::emitDataJs([
                            'id' => $model->id,
                            'description' => Yii::t('gromver.platform', 'Page: {title}', ['title' => $model->title]),
                            'route' => \gromver\platform\basic\modules\menu\models\MenuItem::toRoute($route, ['id' => $model->id]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
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