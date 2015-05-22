<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $itemLayout string
 */

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => $itemLayout,
    'summary' => ''
]);