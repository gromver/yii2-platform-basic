<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Translations
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Translations extends Widget {
    public $model;
    public $options;
    public $linkTemplate = '<a class="btn btn-default" href="{url}">{label}</a>';
    public $labelTemplate = '<button type="button" class="btn btn-primary">{label}</button>';

    public function run()
    {
        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        Html::addCssClass($this->options, 'btn-group btn-group-xs');
        echo Html::tag($tag, $this->renderItems(), $this->options);
    }

    protected function renderItems()
    {
        $items = '';

        foreach($this->model->translations as $language => $item) {
            /** @var $item \gromver\platform\basic\interfaces\ViewableInterface */
            if ($this->model->language === $language) {
                $items = strtr($this->labelTemplate, [
                        '{label}' => $language
                    ]) . $items;
            } else {
                $items .=  strtr($this->linkTemplate, [
                    '{label}' => $language,
                    '{url}' => Yii::$app->urlManager->createUrl($item->getFrontendViewLink(), $language)
                ]);
            }
        }

        return $items;
    }
} 