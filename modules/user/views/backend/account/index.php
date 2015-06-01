<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\user\models\User */

$this->title = Yii::t('gromver.platform', 'Account', [
    'name' => $model->username,
    'id' => $model->id
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Reset Password'), ['reset-password'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-user"></i> ' . Yii::t('gromver.platform', 'Params'), ['params'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'username',
            'email:email',
            'created_at:datetime',
        ],
    ]) ?>

</div>
