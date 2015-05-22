<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\widgets;


use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class TranslationsFrontend
 * TranslationsFrontend применяется во фронте для отображения списка локализаций, относящихся к указанной модели.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TranslationsFrontend extends \yii\bootstrap\Widget
{
    public $model;
    public $options = ['class' => 'translations'];
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
            /** @var $item \gromver\platform\basic\interfaces\model\ViewableInterface */
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