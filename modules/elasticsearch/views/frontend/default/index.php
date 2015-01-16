<?php

/* @var $this yii\web\View */
/* @var $query string */

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Search');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Search');
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="search-default-index">
    <h1><?= $this->title ?></h1>
    <?php echo \gromver\platform\basic\widgets\SearchForm::widget([
        'id' => 'fSearchForm',
        'query' => $query,
        'configureAccess' => 'none'
    ]);

    echo \gromver\platform\basic\widgets\SearchResultsElasticsearch::widget([
        'id' => 'fSearchResult',
        'query' => $query,
        'models' => $this->context->module->getFrontendSearchableModels(),
    ]); ?>
</div>
