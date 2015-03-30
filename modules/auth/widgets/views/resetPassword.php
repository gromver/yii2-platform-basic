<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\bootstrap\ActiveForm $form
 * @var gromver\platform\basic\modules\user\models\User $model
 * @var string|array|null $url
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'reset-password-form',
    'action' => $url,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'password', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password_new'), 'autocomplete' => 'off']) ?>
<?= $form->field($model, 'password_confirm', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password_confirm')]) ?>

<div class="form-group">
    <div classs="text-center">
        <?= Html::submitButton(\Yii::t('gromver.platform', 'Submit'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>