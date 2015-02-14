<?php
/**
 * @var $this yii\web\View
 * @var $model gromver\platform\basic\modules\news\models\Category
 * @var $year integer
 * @var $month integer
 * @var $day integer
 */

use yii\helpers\Html;

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::$app->formatter->asDate(mktime(0,0,0,$month, $day, $year), 'dd MMMM Y');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
    //category breadcrumbs
    if ($model && $menu->isApplicableContext()) {
        //меню ссылается на категорию
        list($route, $params) = $menu->parseUrl();
        $_breadcrumbs = $model->getBreadcrumbs(true);
        $_append = [];
        while (($crumb = array_pop($_breadcrumbs)) && $crumb['url']['id'] != $params['id']) {
            array_unshift($_append, $crumb);
        }
        $this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], $_append);
    }
} else {
    $this->title = Yii::$app->formatter->asDate(mktime(0,0,0,$month, $day, $year), 'dd MMMM Y');
    $this->params['breadcrumbs'] = $model->getBreadcrumbs(true);
}
//$this->params['breadcrumbs'][] = $this->title;
//мета теги
if ($model) {
    if ($model->metakey) {
        $this->registerMetaTag(['name' => 'keywords', 'content' => $model->metakey], 'keywords');
    }
    if ($model->metadesc) {
        $this->registerMetaTag(['name' => 'description', 'content' => $model->metadesc], 'description');
    }
}


echo Html::tag('h2', Html::encode($this->title));

echo \gromver\platform\basic\widgets\PostDay::widget([
    'id' => 'day-posts',
    'category' => $model,
    'year' => $year,
    'month' => $month,
    'day' => $day,
    'context' =>  Yii::$app->menuManager->activeMenu ? Yii::$app->menuManager->activeMenu->path : null
]);