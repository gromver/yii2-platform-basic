<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\tag\models\Tag */

$this->title = Yii::t('gromver.platform', 'Update Tag: {title}', [
    'title' => $model->title
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="tag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
