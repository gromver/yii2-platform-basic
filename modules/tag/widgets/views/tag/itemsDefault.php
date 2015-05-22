<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $itemLayout string
 */

echo \yii\helpers\Html::tag('h1', \yii\helpers\Html::encode($model->title), ['class' => 'page-title page-title_tag']);

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => $itemLayout,
    'summary' => ''
]);