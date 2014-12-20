<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\news\models\Post */
/* @var $sourceModel gromver\platform\basic\news\models\Post */

$this->title = Yii::t('gromver.platform', 'Add Post');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="post-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sourceModel' => $sourceModel
    ]) ?>

</div>
