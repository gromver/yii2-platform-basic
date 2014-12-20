<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \gromver\platform\basic\modules\elasticsearch\models\ActiveDocument */

echo Html::beginTag('div', ['class' => 'search-result-item']);
// todo (array)$model->highlight['text']
echo Html::a($model->highlight['title'][0], $model->getFrontendViewLink(), ['class' => 'h4 title']);
echo Html::tag('p', ($model->hasAttribute('date') ? Html::tag('small', Yii::$app->formatter->asDate($model->date, 'd MMMM Y'), ['class' => 'date']) . ' - ' : '') . implode(' ... ', $model->highlight['text']), ['class' => 'text']);

echo Html::endTag('div');