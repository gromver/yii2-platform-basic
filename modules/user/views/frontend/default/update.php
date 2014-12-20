<?php

/**
 * @var $this yii\web\View
 * @var \gromver\models\ObjectModel $model
 * @var \gromver\platform\basic\user\models\User $user
 */

/** @var \gromver\platform\basic\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'My profile');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'My profile');
}
//$this->params['breadcrumbs'][] = $this->title;

echo \yii\helpers\Html::tag('h2', $this->title);

echo \gromver\platform\frontend\widgets\UserProfile::widget([
    'id' => 'user-profile',
    'model' => $model
]);