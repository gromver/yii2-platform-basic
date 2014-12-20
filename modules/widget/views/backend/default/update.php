<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\widget\models\WidgetConfig */

$this->title = Yii::t('gromver.platform', 'Update Settings for Widget: {id}', [
    'id' => $model->widget_id
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Widget\'s Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->widget_id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="widget-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
