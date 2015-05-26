<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \gromver\platform\basic\modules\news\models\CategorySearch $searchModel
 * @var string $route
 */

$this->title = Yii::t('gromver.platform', 'Select Category');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <?/*<h1><?= Html::encode($this->title) ?></h1>*/?>

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
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'attribute' => 'language',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px',
                'filter' => Yii::$app->getAcceptedLanguagesList()
            ],
            [
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                    /** @var $model \gromver\platform\basic\modules\news\models\Category */
                    return str_repeat(" • ", max($model->level-1, 0)) . $model->title . '<br/>' . Html::tag('small', ' — ' . $model->path, ['class' => 'text-muted']);
                },
                'format' => 'html'

            ],
            [
                'attribute' => 'status',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                    /** @var $model \gromver\platform\basic\modules\news\models\Category */
                    return $model->getStatusLabel();
                },
                'width' => '100px',
                'filter' => \gromver\platform\basic\modules\news\models\Category::statusLabels()
            ],
            [
                'attribute' => 'published_at',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'format' => ['date', 'd MMM Y H:mm'],
                'width' => '160px',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy'
                    ],
                    'type' => \kartik\date\DatePicker::TYPE_RANGE,
                    'attribute2' => 'published_at_to',
                ]
            ],
            [
                'attribute' => 'tags',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model){
                    /** @var $model \gromver\platform\basic\modules\news\models\Category */
                    return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                },
                'filterType' => \dosamigos\selectize\SelectizeDropDownList::className(),
                'filterWidgetOptions' => [
                    'items' => \yii\helpers\ArrayHelper::map(\gromver\platform\basic\modules\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title', 'group'),
                    'clientOptions' => [
                        'maxItems' => 1
                    ],
                    'loadUrl' => ['/grom/tag/backend/default/tag-list']
                ]
            ],
            [
                'header' => Yii::t('gromver.platform', 'Action'),
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) use ($route) {
                    /** @var $model \gromver\platform\basic\modules\news\models\Category */
                    return Html::a(Yii::t('gromver.platform', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \gromver\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('gromver.platform', 'Category: {title}', ['title' => $model->title]),
                            'route' => \gromver\platform\basic\modules\menu\models\MenuItem::toRoute($route, ['id' => $model->id]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->alias
                        ]),
                    ]);
                },
                'width' => '80px',
                'mergeHeader' => true,
                'format' => 'raw'
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