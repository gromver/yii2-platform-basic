<?php
use app\assets\AppAsset;
use yii\helpers\Html;

/**
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
        <div class="container">
            <div class="center-block col-lg-5 col-md-6 col-sm-7" style="float:none;">
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
