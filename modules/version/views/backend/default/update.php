<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\version\models\Version */

$this->title = Yii::t('gromver.platform', 'Update Version: {id}', [
    'id' => $model->id
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Version Manager'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'ID: {id}', [
    'id' => $model->id
]), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
