<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\user\models\User */

$this->title = Yii::t('gromver.platform', 'User: {name} (ID: {id})', [
    'name' => $model->username,
    'id' => $model->id
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', $model->getIsTrashed() ? 'Trash' : 'Users'), 'url' => [$model->getIsTrashed() ? 'index-trash' : 'index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= $model->getIsTrashed() ? null : Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-user"></i> ' . Yii::t('gromver.platform', 'Params'), ['params', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', $model->getIsTrashed() ? 'Delete' : 'Trash It'), [$model->getIsTrashed() ? 'delete' : 'trash', 'id' => $model->id, 'backUrl' => \yii\helpers\Url::to(['index-trash'])], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('gromver.platform', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'password_hash',
            'password_reset_token',
            'auth_key',
            'status',
            'params',
            'created_at:datetime',
            'updated_at:datetime',
            'deleted_at:datetime',
            'last_visit_at:datetime',
        ],
    ]) ?>

</div>
