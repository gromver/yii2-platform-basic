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

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'title', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 100, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>
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
            <?=$form->field($model, 'group', ['wrapperOptions' => ['class' => 'col-sm-9']])->widget(\kartik\select2\Select2::className(), [
                'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                'data' => \yii\helpers\ArrayHelper::map(\gromver\platform\basic\modules\tag\models\Tag::find()->groupBy('group')->andWhere('[[group]] != "" AND [[group]] IS NOT NULL')->all(), 'group', 'group'),
                'pluginOptions' => [
                    'tags' => true,
                    'allowClear' => true,
                    'multiple' => false,
                    'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                    /*'ajax' => [
                        'url' => \yii\helpers\Url::to(['tag-group-list']),
                    ],*/
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'status', ['wrapperOptions' => ['class' => 'col-sm-9']])->dropDownList(['' => Yii::t('gromver.platform', 'Select ...')] + $model->statusLabels()) ?>
        </div>
        <div class="col-sm-6">

        </div>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#meta-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'SEO') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div id="meta-options" class="tab-pane active">
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