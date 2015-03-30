<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $itemLayout string */

\gromver\platform\basic\modules\search\widgets\assets\SearchAsset::register($this);

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => $itemLayout
]);