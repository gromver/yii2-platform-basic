<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page\widgets;


use dosamigos\selectize\SelectizeDropDownList;
use gromver\platform\basic\modules\page\models\Page;
use gromver\widgets\ModalIFrame;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class PageField
 * Поле для списка страниц.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageField extends InputWidget {
    /**
     * @var array
     */
    public $selectizeOptions = [];
    /**
     * Исключает из выборки указанную страницу и ее подстраницы
     * @var integer
     */
    public $excludePageId;

    public function run()
    {
        $selectizeOptions = ArrayHelper::merge([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'name' => $this->name,
            'value' => $this->value,
            'items' => $this->hasModel() ? $this->getItems() : [$this->value => $this->value],
            'loadUrl' => ['/grom/page/backend/default/page-list', 'exclude' => isset($this->excludePageId) ? $this->excludePageId : null],
            'clientOptions' => [
                'plugins' => ['remove_button']
            ]
        ], $this->selectizeOptions);

        echo Html::beginTag('div', ['class' => 'input-group']);

        echo SelectizeDropDownList::widget($selectizeOptions);

        $iFrameHandlerJs = $this->getIFrameHandlerJs();

        echo ModalIFrame::widget([
            'options' => [
                'class' => 'input-group-addon',
                'title' => \Yii::t('gromver.platform', 'Select Page')
            ],
            'label' => '<i class="glyphicon glyphicon-search"></i>',
            'url' => ['/grom/page/backend/default/select', 'modal' => true, 'PageSearch[excludePage]' => isset($this->excludePageId) ? $this->excludePageId : null],
            'handler' => $iFrameHandlerJs
        ]);

        echo Html::endTag('div');

        $this->getView()->registerCss(
<<<CSS
.input-group .selectize-control {
    line-height: 0;
}
.input-group .selectize-control .selectize-input {
    border-radius: 4px 0 0 4px;
    border-right: none;
}
CSS
        );
    }

    protected function getIFrameHandlerJs()
    {
        $inputId = Html::getInputId($this->model, $this->attribute);

        return
<<<JS
function(data) {
    var selectize = $("#{$inputId}")[0].selectize;
    selectize.addOption({value: data.id, text: data.title});
    selectize.addItem(data.id);
}
JS;

    }

    protected function getItems()
    {
        if ($pageId = $this->model->{$this->attribute} and $page = Page::findOne($pageId)) {
            return [$page->id => $page->title];
        } else {
            return [];
        }
    }
} 