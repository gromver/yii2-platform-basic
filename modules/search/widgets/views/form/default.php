<?php

/* @var $this yii\web\View */
/* @var $url string */
/* @var $query string */
/* @var $options array */

use yii\helpers\Html;
?>

<?= Html::beginForm([$url], 'get', $options) ?>

    <div class="input-group">
        <?= Html::textInput('q', $query, ['class' => 'form-control', 'placeholder' => Yii::t('gromver.platform', 'Search ...')]) ?>
        <span class="input-group-btn">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i>', ['class' => 'btn btn-default']) ?>
        </span>
    </div>

<?= Html::endForm() ?>