<?php
/**
 * @var $this yii\web\View
 * @var $model string|\gromver\platform\basic\modules\news\models\Category
 */ ?>

<h1 class="page-title title-category"><?=\yii\helpers\Html::encode($model->title)?></h1>

<?= \gromver\platform\basic\modules\news\widgets\CategoryList::widget([
    'id' => 'cat-cats',
    'category' => $model,
    'context' => $this->context->context
]) ?>