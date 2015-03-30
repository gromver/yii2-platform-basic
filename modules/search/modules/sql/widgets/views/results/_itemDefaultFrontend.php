<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \gromver\platform\basic\modules\search\modules\sql\models\Index */

echo Html::beginTag('div', ['class' => 'search-result-item']);
echo Html::a($this->context->highlightText($model->title), $model->getFrontendViewLink(), ['class' => 'h4 title']);
echo Html::tag('p', (Html::tag('small', Yii::$app->formatter->asDate($model->updated_at, 'd MMMM Y'), ['class' => 'date']) . ' - ') . $this->context->highlightText($model->content), ['class' => 'text']);

echo Html::endTag('div');