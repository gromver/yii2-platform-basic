<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\user\models\User */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 64, 'disabled' => ($model->scenario !== 'create' ? true : false)]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 128, 'disabled' => ($model->scenario !== 'create' ? true : false)]) ?>

    <?= $form->field($model, 'status')->dropDownList(\gromver\platform\basic\modules\user\models\User::statusLabels()) ?>

    <?= $form->field($model, 'password')->passwordInput(['autocomplete'=>'off']) ?>

    <?= $form->field($model, 'password_confirm')->passwordInput(['autocomplete'=>'off']) ?>

    <?php if ($model->getIsSuperAdmin()) {
        echo $form->field($model, 'roles')->textInput(['value' => Yii::t('gromver.platform', 'Super Administrator'), 'disabled' => true]);
    } else {
        echo $form->field($model, 'roles')->listBox(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'), [
            'multiple' => 'multiple'
        ]);
    } ?>

    <div>
        <?= Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php if (!$model->isNewRecord) {
            echo Html::a('<i class="glyphicon glyphicon-user"></i> ' . Yii::t('gromver.platform', 'Params'), ['params', 'id' => $model->id], ['class' => 'btn btn-default']);
        } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
