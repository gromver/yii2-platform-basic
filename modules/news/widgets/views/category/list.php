<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $itemLayout string
 */

echo \yii\widgets\ListView::widget(array_merge([
    'dataProvider' => $dataProvider,
    'itemView' => $itemLayout,
    'summary' => ''
], $this->context->listViewOptions));