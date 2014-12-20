<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\tag\models\TagSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tag-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'language') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'alias') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'group') ?>

    <?php // echo $form->field($model, 'metakey') ?>

    <?php // echo $form->field($model, 'metadesc') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'hits') ?>

    <?php // echo $form->field($model, 'lock') ?>

    <div>
        <?= Html::submitButton(Yii::t('gromver.platform', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('gromver.platform', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
