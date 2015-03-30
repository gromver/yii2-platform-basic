<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\basic\modules\user\models\User $model
 */

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Registration');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Registration');
}
//$this->params['breadcrumbs'][] = $this->title;?>

<div class="form-auth-heading">
    <h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
</div>

<div class="container-fluid">
    <?= \gromver\platform\basic\modules\auth\widgets\AuthSignup::widget([
        'model' => $model,
    ]) ?>
</div>