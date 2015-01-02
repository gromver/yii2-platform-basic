<?php
use yii\helpers\Html;
use app\assets\AppAsset;

/**
 * Макет используется приложением по умолчанию
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= \gromver\platform\basic\widgets\PlatformPanel::widget() ?>
<div class="wrap">
    <div class="container">
        <?= \yii\widgets\Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $body)
            echo \kartik\widgets\Alert::widget([
                'type' => $type,
                'body' => $body
            ]) ?>
        <div class="row">
            <div class="col-sm-3">
                <?= \gromver\platform\basic\widgets\Menu::widget([
                    'id' => 'top-menu',
                    'widgetConfig' => [
                        'heading' => 'Navigation',
                        'class' => \kartik\widgets\SideNav::className(),
                        'activeCssClass' => 'active',
                        'firstItemCssClass' => 'first',
                        'lastItemCssClass' => 'last',
                        //'activateItems'=>true,
                        'activateParents' => true,
                        'options' => ['class' => 'level-1']
                    ]
                ]) ?>
            </div>
            <div class="col-sm-9">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->grom->siteName . ' ' . date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
