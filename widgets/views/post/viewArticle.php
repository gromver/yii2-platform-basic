<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\news\models\Post
 */

use yii\helpers\Html;

if($this->context->showTranslations)
    echo \gromver\platform\basic\widgets\Translations::widget([
        'model' => $model,
        'options' => [
            'class' => 'pull-right'
        ]
    ]);

echo Html::tag('h1', Html::encode($model->title), ['class' => 'page-title title-article']);

if($model->detail_image) echo Html::img($model->getFileUrl('detail_image'), [
    'class' => 'text-block img-responsive',
]);

echo Html::tag('div', $model->detail_text);
