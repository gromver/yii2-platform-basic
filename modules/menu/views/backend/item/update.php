<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\menu\models\MenuItem */
/* @var $linkParamsModel gromver\platform\basic\modules\menu\models\MenuLinkParams */

$this->title = Yii::t('gromver.platform', 'Update Menu Item: {title}', [
    'title' => $model->title
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->menuType->title, 'url' => ['index', 'MenuItemSearch' => ['menu_type_id' => $model->menuType->id]]];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="menu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'linkParamsModel' => $linkParamsModel,
        ]) ?>

</div>
