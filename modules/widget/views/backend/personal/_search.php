<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\widget\models\WidgetConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="widget-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'widget_id') ?>

    <?= $form->field($model, 'widget_class') ?>

    <?= $form->field($model, 'context') ?>

    <?= $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'params') ?>

    <?php // echo $form->field($model, 'valid') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'lock') ?>

    <div>
        <?= Html::submitButton(Yii::t('gromver.platform', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('gromver.platform', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
