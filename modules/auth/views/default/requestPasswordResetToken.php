<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\basic\user\models\User $model
 */

/** @var \gromver\platform\basic\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Forgot Password?');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Forgot Password?');
}
//$this->params['breadcrumbs'][] = $this->title;?>

<div class="form-auth-heading">
    <h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
</div>

<div class="container-fluid">
    <?= \gromver\platform\basic\widgets\AuthRequestPasswordResetToken::widget([
        'id' => 'auth-token',
        'model' => $model
    ]) ?>
</div>
