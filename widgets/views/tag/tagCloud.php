<?php
/**
 * @var $this yii\web\View
 * @var $tags Tag[]
 */

use yii\helpers\Html;
use gromver\platform\basic\tag\models\Tag;

echo Html::beginTag('div', ['class'=>'tag-cloud']);

foreach($tags as $tag) {
    echo Html::a(Html::encode($tag['title']), Tag::viewLink($tag), ['style'=>'font-size: '.($this->context->fontBase + $this->context->fontSpace / $this->context->maxWeight * $tag['weight']).'px', 'class'=>'tag']);
}

echo Html::endTag('div');