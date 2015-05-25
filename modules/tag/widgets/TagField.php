<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\tag\widgets;


use dosamigos\selectize\SelectizeDropDownList;
use gromver\widgets\ModalIFrame;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class TagField
 * Поле для списка тегов. По умолчанию список множественный без ограничения по количеству тегов.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TagField extends InputWidget {
    public $selectizeOptions = [];

    public function run()
    {
        $selectizeOptions = ArrayHelper::merge([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'name' => $this->name,
            'value' => $this->value,
            'options' => [
                'multiple' => true
            ],
            'items' => $this->hasModel() ? ArrayHelper::map($this->model->{$this->attribute}, 'id', 'title', 'group') : $this->value,
            'clientOptions' => [
                'plugins' => ['remove_button'],
                'maxItems' => 'NaN'
            ],
            'loadUrl' => ['/grom/tag/backend/default/tag-list']
        ], $this->selectizeOptions);

        echo Html::beginTag('div', ['class' => 'input-group']);

        echo SelectizeDropDownList::widget($selectizeOptions);

        $iFrameHandlerJs = $this->getIFrameHandlerJs();

        echo ModalIFrame::widget([
            'options' => [
                'class' => 'input-group-addon',
                'title' => \Yii::t('gromver.platform', 'Find Tag')
            ],
            'label' => '<i class="glyphicon glyphicon-search"></i>',
            'url' => ['/grom/tag/backend/default/select', 'modal' => true],
            'handler' => $iFrameHandlerJs
        ]);

        echo ModalIFrame::widget([
            'options' => [
                'class' => 'input-group-addon',
                'title' => \Yii::t('gromver.platform', 'Add Tag')
            ],
            'label' => '<i class="glyphicon glyphicon-plus"></i>',
            'url' => ['/grom/tag/backend/default/create', 'modal' => true],
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
} 