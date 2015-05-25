<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\news\models\Post */
/* @var $sourceModel gromver\platform\basic\modules\news\models\Post */
/* @var $form yii\bootstrap\ActiveForm */
?>

    <div class="post-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['enctype'=>'multipart/form-data']
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
                ])->widget(\gromver\platform\basic\modules\main\widgets\HtmlEditor::className(), [
                    'id' => 'backend-editor',
                    'context' => Yii::$app->controller->getUniqueId(),
                    'model' => $model,
                    'attribute' => 'detail_text'
                ]) ?>

                <?= $form->field($model, 'language')->dropDownList(Yii::$app->getAcceptedLanguagesList(), ['prompt' => Yii::t('gromver.platform', 'Select ...')]) ?>

                <?= $form->field($model, 'category_id')->widget(\kartik\widgets\DepDrop::className(), [
                    'pluginOptions' => [
                        //'initialize' => true,
                        'depends' => [Html::getInputId($model, 'language')],
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        'url' => \yii\helpers\Url::to(['categories', 'selected' => $model->category_id]),
                    ]
                ]) ?>

                <?= $form->field($model, 'status')->dropDownList(['' => Yii::t('gromver.platform', 'Select ...')] + $model->statusLabels()) ?>

                <?= $form->field($model, 'published_at')->widget(\kartik\widgets\DateTimePicker::className(), [
                    'options' => ['value' => date('d.m.Y H:i', is_int($model->published_at) ? $model->published_at : time())],
                    'pluginOptions' => [
                        'format' =>  'dd.mm.yyyy hh:ii',
                        'autoclose' => true,
                    ]
                ]) ?>
            </div>

            <div id="advanced-options" class="tab-pane">
                <?= $form->field($model, 'preview_text')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'ordering')->textInput(['maxlength' => 11]) ?>

                <?= $form->field($model, 'tags')->widget(\dosamigos\selectize\SelectizeDropDownList::className(), [
                    'options' => [
                        'multiple' => true
                    ],
                    'items' => \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title', 'group'),
                    'clientOptions' => [
                        'maxItems' => 'NaN'
                    ],
                    'loadUrl' => ['/grom/tag/backend/default/tag-list']
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
<?php $this->registerJs('$("#' . Html::getInputId($model, 'language') . '").change()', \yii\web\View::POS_READY);