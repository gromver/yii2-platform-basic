<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\page\models\Page
 */

use yii\helpers\Html;

if ($this->context->showTranslations) {
    echo \gromver\platform\basic\widgets\TranslationsFrontend::widget([
        'model' => $model,
        'options' => [
            'class' => 'pull-right'
        ]
    ]);
}
?>
<h1 class="page-title title-page">
    <?= Html::encode($model->title) ?>
</h1>

<div class="page-detail">
    <?= $model->detail_text ?>
</div>