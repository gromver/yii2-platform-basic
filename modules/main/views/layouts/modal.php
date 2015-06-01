<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
\gromver\widgets\ModalIFrameResizerAsset::register($this);

if($debug=Yii::$app->getModule('debug'))
    Yii::$app->getView()->off(View::EVENT_END_BODY, [$debug, 'renderToolbar']);
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
<div class="container-fluid">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $body)
        echo \kartik\alert\Alert::widget([
            'type' => $type,
            'body' => $body
        ]) ?>
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
