<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\page\models\Page */

$this->title = Yii::t('gromver.platform', 'Update Page: {title}', [
    'title' => $model->title
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>

<div class="page-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= \gromver\widgets\ModalIFrame::widget([
            'options' => [
                'class'=>'btn btn-default btn-sm'
            ],
            'label' => '<i class="glyphicon glyphicon-hdd"></i> ' . Yii::t('gromver.platform', 'Versions'),
            'url' => ['/grom/version/backend/default/item', 'item_id' => $model->id, 'item_class' => $model->className()]
        ]) ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
