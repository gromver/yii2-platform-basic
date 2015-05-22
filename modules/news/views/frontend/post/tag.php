<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\tag\models\Tag
 * @var $category \gromver\platform\basic\modules\news\models\Category
 */

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : $model->title;
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
    //category breadcrumbs
    if ($menu->isApplicableContext()) {
        list($route, $params) = $menu->parseUrl();
        if ($route == 'grom/news/frontend/category/view') { //меню ссылается на категорию
            $_breadcrumbs = $category->getBreadcrumbs(true);
            $_append = [];
            while (($crumb = array_pop($_breadcrumbs)) && $crumb['url']['id'] != $params['id']) {
                array_unshift($_append, $crumb);
            }
            $this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], $_append);
        }
    }
} else {
    $this->title = $model->title;
    $this->params['breadcrumbs'] = $category->getBreadcrumbs(true);
}
//$this->params['breadcrumbs'][] = $this->title;
//мета теги
if ($model->metakey) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->metakey], 'keywords');
}
if ($model->metadesc) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->metadesc], 'description');
}


echo \gromver\platform\basic\modules\tag\widgets\TagPosts::widget([
    'id' => 'tag-posts',
    'tag' => $model,
    'categoryId' => $category ? $category->id : null,
    'context' =>  $menu ? $menu->path : null,
]);

$model->hit();