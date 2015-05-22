<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \gromver\models\ObjectModel $model
 * @var yii\bootstrap\ActiveForm $form
 */
?>

<div class="profile-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= \gromver\models\widgets\Fields::widget(['model' => $model]) ?>

    <div>
        <?= Html::submitButton(Yii::t('gromver.platform', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
