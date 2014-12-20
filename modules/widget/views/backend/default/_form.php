<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\widget\models\WidgetConfig */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="widget-config-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->field($model, 'widget_id')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'widget_class')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'context')->textInput(['maxlength' => 1024]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 1024]) ?>

    <?= $form->field($model, 'params')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'valid')->dropDownList(['No', 'Yes']) ?>

    <?/*= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput()*/ ?>

    <?= Html::activeHiddenInput($model, 'lock') ?>

    <div>
        <?= Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
