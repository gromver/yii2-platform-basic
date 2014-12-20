<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\page\models\Page */
/* @var $sourceModel gromver\platform\basic\page\models\Page */

$this->title = Yii::t('gromver.platform', 'Add Page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sourceModel' => $sourceModel
    ]) ?>

</div>
