<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\user\models\User */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('gromver.platform', 'Reset Password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Account'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->field($model, 'password')->passwordInput(['autocomplete'=>'off']) ?>

    <?= $form->field($model, 'password_confirm')->passwordInput(['autocomplete'=>'off']) ?>

    <div>
        <?= Html::submitButton('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
