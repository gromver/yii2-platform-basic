<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\tag\models\Tag */
/* @var $sourceModel gromver\platform\basic\modules\tag\models\Tag */

$this->title = Yii::t('gromver.platform', 'Add Tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sourceModel' => $sourceModel,
    ]) ?>

</div>
