<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\page\models\Page */

$this->title = Yii::t('gromver.platform', 'Update Page: {title}', [
    'title' => $model->title
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['index']];
foreach ($model->parents()->excludeRoots()->all() as $parent) {
    $this->params['breadcrumbs'][] = ['label' => $parent->title, 'url' => ['index', 'PageSearch' => ['parent_id' => $parent->id]]];
}
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>

<div class="page-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
