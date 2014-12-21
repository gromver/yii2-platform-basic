<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\news\models\Category */

$this->title = Yii::t('gromver.platform', 'Update Category: {title}', [
    'title' => $model->title
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>

<div class="category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= \gromver\widgets\ModalIFrame::widget([
            'modalOptions' => [
                'header' => Yii::t('gromver.platform', 'Item Versions Manager - "{title}" (ID:{id})', ['title' => $model->title, 'id' => $model->id]),
                'size' => \yii\bootstrap\Modal::SIZE_LARGE,
            ],
            'buttonContent' => Html::a('<i class="glyphicon glyphicon-hdd"></i> ' . Yii::t('gromver.platform', 'Versions'),
                    ['/grom/version/backend/default/item', 'item_id' => $model->id, 'item_class' => $model->className()], [
                        'class'=>'btn btn-default btn-sm',
                    ]),
            ]) ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
