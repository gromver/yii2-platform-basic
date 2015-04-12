<?php

use yii\helpers\Html;
use gromver\platform\basic\modules\widget\models\WidgetConfigPersonal;

/**
 * @var yii\web\View $this
 * @var gromver\models\ObjectModel $model
 * @var gromver\platform\basic\widgets\Widget $widget
 * @var string $widget_id
 * @var string $widget_class
 * @var string $widget_config
 * @var string $widget_context
 * @var string $selected_context
 * @var string $url
 */

\gromver\platform\basic\modules\widget\assets\WidgetConfigureAsset::register($this); ?>

    <h2><?= $widget->name() ?> <small><?= $widget->description() ?></small></h2>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'layout' => 'horizontal',
    'options' => [
        'class' => 'form-configure'
    ],
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
        ]
    ]
]); ?>

<?= Html::hiddenInput('widget_id', $widget_id) ?>

<?= Html::hiddenInput('widget_class', $widget_class) ?>

<?= Html::hiddenInput('widget_context', $widget_context) ?>

<?= Html::hiddenInput('selected_context', $selected_context) ?>

<?= Html::hiddenInput('url', $url) ?>

<?= Html::hiddenInput('widget_config', $widget_config) ?>

    <div class="controls">
        <div class="controls-bar well">
            <div class="controls-bar__actions">
                <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-save"></span> ' . Yii::t('gromver.platform', 'Save'), ['class' => 'btn btn-success', 'name'=>'task', 'value'=>'save']) ?>
                <?php if (WidgetConfigPersonal::find()->where(['widget_id' => $widget_id, 'context' => $selected_context])->exists()) {
                    echo Html::submitButton('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('gromver.platform', 'Delete'), ['class' => 'btn btn-danger', 'name'=>'task', 'value'=>'delete']);
                } ?>
            </div>
            <div class="controls-bar__method">
                <?= Html::checkbox('bulk-method', false, ['label' => Yii::t('gromver.platform', 'Apply action to the subordinated contexts')])?>
            </div>
        </div>

        <div class="context-bar form-group">
            <?= $this->render('_contexts', [
                'widget_id' => $widget_id,
                'widget_context' => $widget_context,
                'selected_context' => $selected_context,
                'loaded_context' => $widget->getLoadedContext()
            ]); ?>
        </div>
    </div>

<?= \gromver\models\widgets\Fields::widget(['model' => $model]) ?>

<?php \yii\bootstrap\ActiveForm::end(); ?>

<?php $this->registerJs('$("#'.$form->getId().'").on("refresh.form", function(){
    $(this).find("button[value=\'refresh\']").click()
})'); ?>