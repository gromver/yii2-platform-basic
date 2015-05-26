<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var gromver\platform\basic\modules\news\models\Category $model
 * @var gromver\platform\basic\modules\news\models\Category $sourceModel
 * @var yii\bootstrap\ActiveForm $form
 */

\gromver\widgets\ParseUrlAsset::register($this); ?>

<div class="category-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'title', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 1024, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'alias', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 255, 'placeholder' => Yii::t('gromver.platform', 'Auto-generate')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'language', ['wrapperOptions' => ['class' => 'col-sm-9']])->dropDownList(Yii::$app->getAcceptedLanguagesList(), ['prompt' => Yii::t('gromver.platform', 'Select ...')]) ?>
        </div>
        <div class="col-sm-6">
            <?php
            $idLanguage = Html::getInputId($model, 'language');
            $idParent_id = Html::getInputId($model, 'parent_id');
            echo $form->field($model, 'parent_id', [
                'wrapperOptions' => ['class' => 'col-sm-9'],
                'inputTemplate' => '<div class="input-group select2-bootstrap-append">{input}' . \gromver\widgets\ModalIFrame::widget([
                        'options' => [
                            'class' => 'input-group-addon',
                            'title' => \Yii::t('gromver.platform', 'Select Category'),
                        ],
                        'label' => '<i class="glyphicon glyphicon-folder-open"></i>',
                        'url' => ['select', 'modal' => true, 'CategorySearch[excludeCategory]' => $model->isNewRecord ? null : $model->id],
                        'handler' =>
<<<JS
function(data) {
    $("#{$idParent_id}").html('<option value="' + data.id + '">' + data.title + '</option>').val(data.id).trigger('change');
}
JS
                        ,
                        'actionHandler' => 'function(url) {return (new URI(url)).addSearch("CategorySearch[language]", $("#' . $idLanguage . '").val())}'
                    ]) . '</div>',
            ])->widget(\kartik\select2\Select2::className(), [
                'initValueText' => $model->parent ? $model->parent->title : null,
                'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['category-list', 'exclude' => $model->isNewRecord ? null : $model->id, 'selected' => $model->parent_id]),
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term, language:$("#' . $idLanguage .'").val()}; }')
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'status', ['wrapperOptions' => ['class' => 'col-sm-9']])->dropDownList($model->statusLabels()) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'published_at', ['wrapperOptions' => ['class' => 'col-sm-9']])->widget(\kartik\widgets\DateTimePicker::className(), [
                'options' => ['value' => date('d.m.Y H:i', is_int($model->published_at) ? $model->published_at : time())],
                'pluginOptions' => [
                    'format' =>  'dd.mm.yyyy hh:ii',
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?php
            $idTags = Html::getInputId($model, 'tags');
            $handlerJs = <<<JS
function(data) {
    var select = $("#{$idTags}").append('<option value="' + data.id + '">' + data.title + '</option>'),
        selectedValues = select.val() || [];
        selectedValues.push(data.id);

    select.val($.unique(selectedValues)).trigger('change');
}
JS;
            echo $form->field($model, 'tags', [
                'wrapperOptions' => ['class' => 'col-sm-9'],
                'inputTemplate' => '<div class="input-group select2-bootstrap-append">{input}' . \gromver\widgets\ModalIFrame::widget([
                        'options' => [
                            'class' => 'input-group-addon',
                            'title' => \Yii::t('gromver.platform', 'Select Tag'),
                        ],
                        'label' => '<i class="glyphicon glyphicon-folder-open"></i>',
                        'url' => ['/grom/tag/backend/default/select', 'modal' => true],
                        'handler' => $handlerJs,
                        'actionHandler' => 'function(url) {return (new URI(url)).addSearch("TagSearch[language]", $("#' . $idLanguage . '").val())}'
                    ]) . \gromver\widgets\ModalIFrame::widget([
                        'options' => [
                            'class' => 'input-group-addon',
                            'title' => \Yii::t('gromver.platform', 'Add Tag'),
                        ],
                        'label' => '<i class="glyphicon glyphicon-plus"></i>',
                        'url' => ['/grom/tag/backend/default/create', 'modal' => true],
                        'handler' => $handlerJs,
                        'actionHandler' => 'function(url) {return (new URI(url)).addSearch("language", $("#' . $idLanguage . '").val())}'
                    ]) . '</div>',
            ])->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'),
                'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                'pluginOptions' => [
                    'tags' => true,
                    'multiple' => true,
                    'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/grom/tag/backend/default/tag-list']),
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term, language:$("#' . $idLanguage .'").val()}; }')
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'ordering', ['horizontalCssClasses' => ['wrapper' => 'col-xs-8 col-sm-4', 'label' => 'col-xs-4 col-sm-3']])->textInput() ?>
        </div>
    </div>

    <?//= $form->field($model, 'versionNote')->textInput() ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#main-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Description') ?></a></li>
        <li><a href="#advanced-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Preview') ?></a></li>
        <li><a href="#meta-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'SEO') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div id="main-options" class="tab-pane active">
            <?= $form->field($model, 'detail_text', [
                'horizontalCssClasses' => [
                    'label' => 'col-xs-12',
                    'wrapper' => 'col-xs-12'
                ],
                'inputOptions' => ['class' => 'form-control']
            ])->widget(\gromver\platform\basic\modules\main\widgets\HtmlEditor::className(), [
                'id' => 'backend-editor',
                'context' => Yii::$app->controller->getUniqueId(),
                'model' => $model,
                'attribute' => 'detail_text'
            ]) ?>

            <?= $form->field($model, 'detail_image')->widget(\kartik\file\FileInput::classname(), [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'initialPreview' => $model->getFileUrl('detail_image') ? [Html::img($model->getFileUrl('detail_image'), ['class' => 'file-preview-image'])] : []/*new \yii\web\JsExpression('undefined')*/,
                    'initialPreviewConfig' => [[
                        'caption' => $model->getFileUrl('detail_image'),
                        'url' => \yii\helpers\Url::to(['delete-file', 'pk' => $model->id, 'attribute' => 'detail_image']),
                    ]],
                    'showUpload' => false,
                    'showRemove' => false,
                ]
            ]) ?>

            <?//= $form->field($model, 'path')->textInput(['maxlength' => 2000]) ?>
        </div>
        <div id="advanced-options" class="tab-pane">
            <?= $form->field($model, 'preview_text', [
                'horizontalCssClasses' => [
                    'label' => 'col-xs-12',
                    'wrapper' => 'col-xs-12'
                ],
            ])->textarea(['rows' => 10]) ?>

            <?= $form->field($model, 'preview_image')->widget(\kartik\file\FileInput::classname(), [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'initialPreview' => $model->getFileUrl('preview_image') ? [Html::img($model->getFileUrl('preview_image'), ['class' => 'file-preview-image'])] : []/* new \yii\web\JsExpression('undefined')*/,
                    'initialPreviewConfig' => [[
                        'caption' => $model->getFileUrl('preview_image'),
                        'url' => \yii\helpers\Url::to(['delete-file', 'pk' => $model->id, 'attribute' => 'preview_image']),
                    ]],
                    'showUpload' => false,
                    'showRemove' => false,
                ]
            ]) ?>
        </div>
        <div id="meta-options" class="tab-pane">
            <?= $form->field($model, 'metakey')->textarea(['maxlength' => 255]) ?>

            <?= $form->field($model, 'metadesc')->textarea(['maxlength' => 2000]) ?>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'lock') ?>

    <div>
        <?php if ($model->isNewRecord) {
            echo Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        } else {
            echo Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            echo ' ';
            echo \gromver\platform\basic\modules\version\widgets\Versions::widget([
                'model' => $model
            ]);
        } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJs('$("#' . Html::getInputId($model, 'language') . '").change()', \yii\web\View::POS_READY);