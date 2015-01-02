<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\menu\models\MenuItem */
/* @var $sourceModel gromver\platform\basic\modules\menu\models\MenuItem */
/* @var $linkParamsModel gromver\platform\basic\modules\menu\models\MenuLinkParams */

$this->title = Yii::t('gromver.platform', 'Add Menu Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['/grom/menu/backend/type/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'sourceModel' => $sourceModel,
            'linkParamsModel' => $linkParamsModel,
        ]) ?>

</div>
