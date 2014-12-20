<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\version\models\VersionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'item_class') ?>

    <?= $form->field($model, 'version_note') ?>

    <?= $form->field($model, 'version_hash') ?>

    <?php // echo $form->field($model, 'version_data') ?>

    <?php // echo $form->field($model, 'character_count') ?>

    <?php // echo $form->field($model, 'keep_forever') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div>
        <?= Html::submitButton(Yii::t('gromver.platform', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('gromver.platform', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
