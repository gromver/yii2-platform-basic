<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\basic\user\models\User $model
 */
?>
<?php $form = ActiveForm::begin([
    'id' => 'signup-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'username', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-user"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

<?= $form->field($model, 'email', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-envelope"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('email'), 'type' => 'email', 'autocomplete'=>'off']) ?>

<?= $form->field($model, 'password', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'autocomplete'=>'off']) ?>

<?php if ($model->scenario == 'signupWithCaptcha')
    echo $form->field($model, 'verifyCode'/*, ['template'=>'{label}{input}{error}']*/)->widget(Captcha::className(), ['captchaAction' => 'default/captcha', 'options' => ['class' => 'form-control']]) ?>

<div class="form-group">
    <div class="text-center">
        <?= Html::submitButton(\Yii::t('gromver.platform', 'Signup'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
