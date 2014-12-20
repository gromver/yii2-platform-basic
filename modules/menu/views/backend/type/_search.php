<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\menu\models\MenuTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'parent_id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'language') ?>

    <?= $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'alias') ?>

    <?php // echo $form->field($model, 'path') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'link') ?>

    <?php // echo $form->field($model, 'link_type') ?>

    <?php // echo $form->field($model, 'link_params') ?>

    <?php // echo $form->field($model, 'layout_path') ?>

    <?php // echo $form->field($model, 'access_rule') ?>

    <?php // echo $form->field($model, 'metakey') ?>

    <?php // echo $form->field($model, 'metadesc') ?>

    <?php // echo $form->field($model, 'robots') ?>

    <?php // echo $form->field($model, 'secure') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'root') ?>

    <?php // echo $form->field($model, 'lft') ?>

    <?php // echo $form->field($model, 'rgt') ?>

    <?php // echo $form->field($model, 'level') ?>

    <?php // echo $form->field($model, 'ordering') ?>

    <?php // echo $form->field($model, 'hits') ?>

    <?php // echo $form->field($model, 'lock') ?>

    <div>
        <?= Html::submitButton(Yii::t('gromver.platform', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('gromver.platform', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
