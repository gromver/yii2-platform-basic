<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\menu\models\MenuType */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['delete', 'id' => $model->id], [
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
            'title',
            'alias',
            'note',
            'created_at:datetime',
            'updated_at:datetime',
            'created_by',
            'updated_by',
            'lock',
        ],
    ]) ?>

</div>
