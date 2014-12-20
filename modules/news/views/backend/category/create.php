<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\news\models\Category */
/* @var $sourceModel gromver\platform\basic\modules\news\models\Category */

$this->title = Yii::t('gromver.platform', 'Add Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sourceModel' => $sourceModel
    ]) ?>

</div>
