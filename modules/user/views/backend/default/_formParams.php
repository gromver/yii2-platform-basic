<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\models\ObjectModel*/
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= \gromver\models\widgets\Fields::widget(['model' => $model]) ?>

    <div>
        <?= Html::submitButton('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
