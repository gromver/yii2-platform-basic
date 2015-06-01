<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user gromver\platform\basic\modules\user\models\User */
/* @var $model gromver\models\ObjectModel */

$this->title = Yii::t('gromver.platform', 'Update User Params: {name} (ID: {id})', [
    'id' => $user->id,
    'name' => $user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Account'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Params');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formParams', [
        'model' => $model,
    ]) ?>

</div>
