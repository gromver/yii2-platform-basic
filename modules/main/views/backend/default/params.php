<?php

use yii\helpers\Html;

/**
 * @var gromver\models\ObjectModel $model
 * @var yii\web\View $this
 */
$this->title = Yii::t('gromver.platform', 'System Configuration');
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="page-heading">
    <h2><?= \yii\helpers\Html::encode($this->title) ?></h2>
</div>

<div class="config-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= \gromver\models\widgets\Fields::widget(['model' => $model]) ?>

    <div>
        <?= Html::submitButton('<i class="glyphicon glyphicon-save"></i> ' . Yii::t('gromver.platform', 'Save'), ['class' => 'btn btn-success']) ?>
        <?//= Html::submitButton('<i class="glyphicon glyphicon-refresh"></i> ' . Yii::t('gromver.platform', 'Refresh'), ['class' => 'btn btn-default', 'name' => 'task', 'value' => 'refresh']) ?>
    </div>

    <?php \yii\bootstrap\ActiveForm::end(); ?>

</div>

<?php /*$this->registerJs('$("#'.$form->getId().'").on("refresh.form", function(){
    $(this).find("button[value=\'refresh\']").click()
})'); */?>