<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\page\models\Page
 */

use yii\helpers\Html;

$this->registerAssetBundle(\gromver\platform\basic\modules\page\widgets\assets\PageAsset::className());

if ($this->context->showTranslations) {
    echo \gromver\platform\basic\modules\main\widgets\TranslationsFrontend::widget([
        'model' => $model,
    ]);
}
?>
<h1 class="page-title title-page">
    <?= Html::encode($model->title) ?>
</h1>

<div class="page-detail">
    <?= $model->detail_text ?>
</div>