<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\basic\tag\models\TagToItem
 * @var $key string
 * @var $index integer
 * @var $widget \yii\widgets\ListView
 */

use yii\helpers\Html;

if ($item = $model->item) {
    if($item instanceof \gromver\platform\basic\interfaces\ViewableInterface) {
        /** @var $item \yii\db\ActiveRecord|\gromver\platform\basic\interfaces\ViewableInterface */
        echo Html::tag('h4', Html::a(Html::encode($item->title), $item->getViewLink()));

    } else {
        echo Html::tag('h4', Html::encode($item->title));
    }
}

