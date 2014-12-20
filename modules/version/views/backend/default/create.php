<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\version\models\Version */

$this->title = Yii::t('gromver.platform', 'Create Version');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Version Manager'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
