<?php
/**
 * @var $this yii\web\View
 * @var $widget gromver\platform\basic\widgets\PlatformPanel
 */

use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;

$this->registerAssetBundle(\gromver\platform\basic\widgets\assets\PlatformAsset::className());

$navBar = NavBar::begin(\yii\helpers\ArrayHelper::merge([
    'brandLabel' => Yii::$app->grom->siteName,
    'brandUrl' => ['/grom/backend/default/index'],
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top platform-panel'
    ],
], $widget->options)); ?>

<?= \gromver\platform\basic\modules\search\widgets\SearchFormBackend::widget([
    'id' => 'bPanelForm',
    'options' => ['class' => 'navbar-form navbar-left'],
    'query' => ''
]); ?>

<?php
$menuItems = [
    ['label' => Yii::t('gromver.platform', 'System'), 'items' => [
        ['label' => Yii::t('gromver.platform', 'Control Panel'), 'url' => ['/grom/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Configuration'), 'url' => ['/grom/backend/default/params']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['/grom/user/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Flush Cache'), 'url' => ['/grom/backend/default/flush-cache']],
    ]],
    ['label' => Yii::t('gromver.platform', 'Menu'), 'items' => array_merge([
        ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['/grom/menu/backend/type/index']],
        ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['/grom/menu/backend/item/index']],
        '<li class="divider"></li>',
    ], array_map(function ($value) {
        /** @var $value \gromver\platform\basic\modules\menu\models\MenuType */
        return ['label' => $value->title, 'url' => ['/grom/menu/backend/item/index', 'MenuItemSearch' => ['menu_type_id' => $value->id]]];
    }, \gromver\platform\basic\modules\menu\models\MenuType::find()->all()))],
    ['label' => Yii::t('gromver.platform', 'Content'), 'items' => [
        ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/grom/page/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['/grom/news/backend/category/index']],
        ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['/grom/news/backend/post/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Tags'), 'url' => ['/grom/tag/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Media Manager'), 'url' => ['/grom/media/backend/default/index']],
    ]],
    ['label' => Yii::t('gromver.platform', 'Components'), 'items' => [
        ['label' => Yii::t('gromver.platform', 'Version Manager'), 'url' => ['/grom/version/backend/default/index']],
        ['label' => Yii::t('gromver.platform', "Widget's Settings"), 'url' => ['/grom/widget/backend/default/index']],
        ['label' => Yii::t('gromver.platform', 'Search'), 'url' => ['/grom/sqlsearch/backend/default/index']],
    ]],
];
if (Yii::$app->user->isGuest) {
    $menuItems[] = ['label' => Yii::t('gromver.platform', 'Login'), 'url' => Yii::$app->user->loginUrl];
} else {
    $menuItems[] = [
        'label' => '<i class="glyphicon glyphicon-user"></i> ' . Yii::$app->user->identity->username,
        'items' => [
            ['label' => '<i class="glyphicon glyphicon-home"></i> ' . Yii::t('gromver.platform', 'Home'), 'url' => Yii::$app->homeUrl],
            ['label' => '<i class="glyphicon glyphicon-envelope"></i> ' . Yii::t('gromver.platform', 'Contact'), 'url' => ['/grom/backend/default/contact']],
            '<li class="divider"></li>',
            ['label' => '<i class="glyphicon glyphicon-cog"></i> ' . Yii::t('gromver.platform', 'Profile'), 'url' => ['/grom/user/backend/default/update', 'id' => Yii::$app->user->id]],
            ['label' => '<i class="glyphicon glyphicon-log-out"></i> ' . Yii::t('gromver.platform', 'Logout'), 'url' => ['/grom/auth/default/logout']]
        ]
    ];
} ?>
<div class="navbar-right">

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItems,
        'encodeLabels' => false
    ]) ?>

    <div class="input-group navbar-left">
        <?= Html::tag('span', Yii::t('gromver.platform', 'Language'), ['class' => 'navbar-text']) . '&nbsp;' ?>
        <div class="btn-group">
            <?= implode('', array_map(function($language) {
                return Html::a($language, Yii::$app->urlManager->createUrl([Yii::$app->request->getPathInfo()] + Yii::$app->request->getQueryParams(), $language), ['class' => 'btn navbar-btn btn-xs' . ($language === Yii::$app->language ? ' btn-primary active' : ' btn-default')]);
            }, Yii::$app->acceptedLanguages)) ?>
        </div>
    </div>

</div>
<style>
    <?= '#' . $navBar->id ?> .navbar-right { margin-right: 0; }
</style>
<?php NavBar::end() ?>
