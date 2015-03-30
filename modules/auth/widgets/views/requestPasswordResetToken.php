<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\basic\modules\user\models\User $model
 * @var string|array|null $url
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'request-password-reset-token-form',
    'action' => $url,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'email', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-envelope"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

<div class="form-group">
    <?= Html::submitButton(\Yii::t('gromver.platform', 'Submit'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
</div>

<?php ActiveForm::end(); ?>