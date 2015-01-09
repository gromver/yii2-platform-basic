<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var gromver\platform\basic\modules\news\models\Category $model
 * @var gromver\platform\basic\modules\news\models\Category $sourceModel
 * @var yii\bootstrap\ActiveForm $form
 */
?>

    <div class="category-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>

        <?= $form->errorSummary($model) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => 1000, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>

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
                <div class="form-group container">
                    <?= Html::activeLabel($model, 'detail_text') ?>
                    <div>
                        <?= \mihaildev\ckeditor\CKEditor::widget([
                            'model' => $model,
                            'attribute' => 'detail_text',
                            'editorOptions' => \mihaildev\elfinder\ElFinder::ckeditorOptions('grom/media/manager', [
                                'filebrowserBrowseUrl' => ['/grom/menu/backend/item/ckeditor-select'],
                                'extraPlugins' => 'codesnippet'
                            ])
                        ]) ?>
                    </div>
                </div>
                <?= $form->field($model, 'language')->dropDownList(Yii::$app->getAcceptedLanguagesList(), ['prompt' => Yii::t('gromver.platform', 'Select ...'), 'id' => 'language']) ?>

                <?= $form->field($model, 'parent_id')->widget(\kartik\widgets\DepDrop::className(), [
                    'pluginOptions' => [
                        //'initialize' => true,
                        'depends' => ['language'],
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        'url' => \yii\helpers\Url::to(['categories', 'update_item_id' => $model->isNewRecord ? null : $model->id, 'selected' => $model->parent_id]),
                    ]
                ]) ?>

                <?//= $form->field($model, 'path')->textInput(['maxlength' => 2000]) ?>

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

                <?= $form->field($model, 'ordering')->textInput() ?>

                <?= $form->field($model, 'tags')->widget(\dosamigos\selectize\Selectize::className(), [
                    'options' => [
                        'multiple' => true
                    ],
                    'items' => \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title', 'group'),
                    'clientOptions' => [
                        'maxItems' => 'NaN'
                    ],
                    'url' => ['/grom/tag/backend/default/tag-list']
                ]) ?>

                <?= $form->field($model, 'detail_image')->widget(\gromver\platform\basic\widgets\FileInput::classname(), [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => ['showUpload' => false]
                ]) ?>

                <?= $form->field($model, 'preview_image')->widget(\gromver\platform\basic\widgets\FileInput::classname(), [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => ['showUpload' => false]
                ]) ?>
            </div>
            <div id="meta-options" class="tab-pane">
                <?= $form->field($model, 'metakey')->textarea(['maxlength' => 255]) ?>

                <?= $form->field($model, 'metadesc')->textarea(['maxlength' => 2000]) ?>
            </div>
        </div>

        <?= Html::activeHiddenInput($model, 'lock') ?>

        <div>
            <?= Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php $this->registerJs('$("#language").change()', \yii\web\View::POS_READY);