<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\user\models\User */

$this->title = Yii::t('gromver.platform', 'Update User: {name} (ID: {id})', [
    'name' => $model->username,
    'id' => $model->id
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username . " (ID: $model->id)", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
