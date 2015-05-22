<?php

/**
 * @var $this yii\web\View
 */

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Dummy Page');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Dummy Page');
}
//$this->params['breadcrumbs'][] = $this->title;
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow'], 'robots');

echo \gromver\platform\basic\modules\main\widgets\DummyPage::widget([
    'id' => 'dummy-page'
]);