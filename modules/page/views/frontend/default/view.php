<?php
/**
 * @var $this yii\web\View
 * @var $model gromver\platform\basic\modules\page\models\Page
 */

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : $model->title;
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = $model->title;
}
//$this->params['breadcrumbs'][] = $this->title;
//мета теги
if ($model->metakey) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->metakey], 'keywords');
}
if ($model->metadesc) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->metadesc], 'description');
}


echo \gromver\platform\basic\widgets\PageView::widget([
    'id' => 'page-view',
    'page' => $model,
]);