<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\modules\news\models\Post
 */

use yii\helpers\Html;

\gromver\platform\basic\modules\news\widgets\assets\PostAsset::register($this);

if($this->context->showTranslations)
    echo \gromver\platform\basic\modules\main\widgets\TranslationsFrontend::widget([
        'model' => $model,
    ]);
?>

<h1 class="page-title title-article">
    <?= Html::encode($model->title) ?>
</h1>'

<?php if($model->detail_image) echo Html::img($model->getFileUrl('detail_image'), [
    'class' => 'text-block img-responsive',
]); ?>
<div class="article-detail">
    <?= $model->detail_text ?>
</div>