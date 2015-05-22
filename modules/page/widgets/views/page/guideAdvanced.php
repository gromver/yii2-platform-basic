<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\page\models\Page
 * @var $rootModel \gromver\platform\basic\modules\page\models\Page
 * @var $prevModel \gromver\platform\basic\modules\page\models\Page|null
 * @var $nextModel \gromver\platform\basic\modules\page\models\Page|null
 * @var $items \gromver\platform\basic\modules\page\models\Page[]
 */

use yii\helpers\Html;

$this->registerAssetBundle(\gromver\platform\basic\modules\page\widgets\assets\PageAsset::className()); ?>

<h1 class="page-title title-page">
    <?= Html::encode($model->title) ?>
</h1>

<div class="row">
    <div class="col-md-8">
        <div class="page-detail">
            <?php if ($this->context->showTranslations) {
                echo \gromver\platform\basic\modules\main\widgets\TranslationsFrontend::widget([
                    'model' => $model,
                ]);
            } ?>
            <?= $model->detail_text ?>
        </div>

        <div class="page-nav">
            <?php if ($prevModel) {
                echo \yii\helpers\Html::a('<< ' . $prevModel->title, $prevModel->getFrontendViewLink(), ['class' => 'pull-left']);
            }
            if ($nextModel) {
                echo \yii\helpers\Html::a($nextModel->title . ' >>', $nextModel->getFrontendViewLink(), ['class' => 'pull-right']);
            } ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="page-list">
            <?= $this->render('_itemsAdvanced', [
                'items' => $items,
                'level' => 1,
                'collapse' => false,
                'id' => $this->context->id . '-u' . $rootModel->id
            ]) ?>
        </div>
    </div>
</div>