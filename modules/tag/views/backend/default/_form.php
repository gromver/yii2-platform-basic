<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var gromver\platform\basic\modules\tag\models\Tag $model
 * @var gromver\platform\basic\modules\tag\models\Tag $sourceModel
 * @var yii\bootstrap\ActiveForm $form
 */
?>

<div class="tag-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 100, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => 255, 'placeholder' => Yii::t('gromver.platform', 'Auto-generate')]) ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#main-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Main') ?></a></li>
        <li><a href="#meta-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Metadata') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div id="main-options" class="tab-pane active">
            <?= $form->field($model, 'language')->dropDownList(Yii::$app->getAcceptedLanguagesList(), ['prompt' => Yii::t('gromver.platform', 'Select ...')]) ?>

            <?= $form->field($model, 'status')->dropDownList(['' => Yii::t('gromver.platform', 'Select ...')] + $model->statusLabels()) ?>

            <?= $form->field($model, 'group')->textInput(['maxlength' => 255, 'placeholder' => isset($sourceModel) ? $sourceModel->group : null]) ?>
        </div>
        <div id="meta-options" class="tab-pane">
            <?= $form->field($model, 'metakey')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'metadesc')->textarea(['maxlength' => 2048]) ?>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'lock') ?>

    <div>
        <?= Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>