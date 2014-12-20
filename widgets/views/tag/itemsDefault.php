<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $itemLayout string
 */

echo \yii\helpers\Html::tag('h2', \yii\helpers\Html::encode($model->title));

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => $itemLayout,
    'summary' => ''
]);