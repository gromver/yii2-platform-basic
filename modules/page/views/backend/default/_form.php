<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\page\models\Page */
/* @var $sourceModel gromver\platform\basic\modules\page\models\Page */
/* @var $form yii\bootstrap\ActiveForm */?>

<div class="page-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 1024, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => 255, 'placeholder' => Yii::t('gromver.platform', 'Auto-generate')]) ?>

    <?= $form->field($model, 'versionNote')->textInput() ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#main-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Main') ?></a></li>
        <li><a href="#advanced-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Advanced') ?></a></li>
        <li><a href="#meta-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Metadata') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div id="main-options" class="tab-pane active">
            <?= $form->field($model, 'detail_text', [
                'horizontalCssClasses' => [
                    'label' => 'col-xs-12',
                    'wrapper' => 'col-xs-12'
                ]
            ])->widget(\gromver\platform\basic\widgets\HtmlEditor::className(), [
                'id' => 'backend-editor',
                'context' => Yii::$app->controller->getUniqueId(),
                'model' => $model,
                'attribute' => 'detail_text'
            ]) ?>

            <?= $form->field($model, 'language')->dropDownList(Yii::$app->getAcceptedLanguagesList(), ['prompt' => Yii::t('gromver.platform', 'Select ...'), 'id' => 'language']) ?>

            <?= $form->field($model, 'parent_id')->widget(\kartik\widgets\DepDrop::className(), [
                'pluginOptions' => [
                    //'initialize' => true,
                    'depends' => ['language'],
                    'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                    'url' => \yii\helpers\Url::to(['pages', 'update_item_id' => $model->isNewRecord ? null : $model->id, 'selected' => $model->parent_id]),
                ]
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList($model->statusLabels()) ?>

        </div>

        <div id="advanced-options" class="tab-pane">
            <?= $form->field($model, 'preview_text')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'ordering')->textInput() ?>

            <?= $form->field($model, 'tags')->widget(\dosamigos\selectize\SelectizeDropDownList::className(), [
                'options'=>[
                    'multiple'=>true
                ],
                'items' => \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title', 'group'),
                'clientOptions' => [
                    'maxItems' => 'NaN'
                ],
                'loadUrl' => ['/grom/tag/backend/default/tag-list']
            ]) ?>
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
<?php $this->registerJs('$("#language").change()', \yii\web\View::POS_READY);