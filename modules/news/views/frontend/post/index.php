<?php
/**
 * @var $this yii\web\View
 */

use yii\helpers\Html;

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'News');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'News');
}
//$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h2', Html::encode($this->title));

echo \gromver\platform\basic\modules\news\widgets\PostList::widget([
    'id' => 'post-index',
    'context' =>  $menu ? $menu->path : null,
]);