<?php
/**
 * @var $this yii\web\View
 */

use yii\helpers\Html;

/** @var \gromver\platform\basic\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'News');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'News');
}
//$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h2', Html::encode($this->title));

echo \gromver\platform\basic\widgets\PostList::widget([
    'id' => 'post-index',
    'context' =>  Yii::$app->menuManager->activeMenu ? Yii::$app->menuManager->activeMenu->path : null,
]);