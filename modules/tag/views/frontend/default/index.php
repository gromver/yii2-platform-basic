<?php
/**
 * @var $this yii\web\View
 */

/** @var \gromver\platform\basic\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Tag cloud');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Tag cloud');
}
//$this->params['breadcrumbs'][] = $this->title;


echo \yii\helpers\Html::tag('h2', \yii\helpers\Html::encode($this->title));

echo \gromver\platform\basic\widgets\TagCloud::widget([
    'id' => 'tag-cloud',
    'language' => Yii::$app->language
]);