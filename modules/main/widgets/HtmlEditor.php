<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\widgets;


use gromver\platform\basic\modules\widget\widgets\WidgetPersonal;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use vova07\imperavi\Widget;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Class HtmlEditor
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class HtmlEditor extends WidgetPersonal {
    const EDITOR_CKEDITOR_BASIC = 'ckeditor_basic';
    const EDITOR_CKEDITOR_STANDARD = 'ckeditor_standard';
    const EDITOR_CKEDITOR_FULL = 'ckeditor_full';
    const EDITOR_IMPERAVI = 'imperavi';
    const EDITOR_TEXTAREA = 'textarea';
    /**
     * @var Model the data model that this widget is associated with.
     * @ignore
     */
    public $model;
    /**
     * @var string the model attribute that this widget is associated with.
     * @ignore
     */
    public $attribute;
    /**
     * @var string the input name. This must be set if [[model]] and [[attribute]] are not set.
     * @ignore
     */
    public $name;
    /**
     * @var string the input value.
     * @ignore
     */
    public $value;
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @ignore
     */
    public $options = ['style' => 'width: 100%; height: 300px;'];
    /**
     * @var string
     * @field list
     * @items editorLabels
     */
    public $editor = self::EDITOR_CKEDITOR_FULL;


    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        if ($this->name === null && !$this->hasModel()) {
            throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        parent::init();
    }

    /**
     * @return boolean whether this widget is associated with a data model.
     */
    protected function hasModel()
    {
        return $this->model instanceof Model && $this->attribute !== null;
    }

    protected function launch()
    {
        switch ($this->editor) {
            case self::EDITOR_CKEDITOR_BASIC:
                echo CKEditor::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'name' => $this->name,
                    'value' => $this->value,
                    'options' => ['class' => 'form-control', 'rows' => 10],
                    'editorOptions' => ElFinder::ckeditorOptions('grom/media/manager', [
                        'filebrowserBrowseUrl' => ['/grom/menu/backend/item/ckeditor-select'],
                        'extraPlugins' => 'codesnippet',
                        'preset' => 'basic',
                        'tabSpaces' => 4
                    ])
                ]);
                break;
            case self::EDITOR_CKEDITOR_STANDARD:
                echo CKEditor::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'name' => $this->name,
                    'value' => $this->value,
                    'options' => ['class' => 'form-control', 'rows' => 10],
                    'editorOptions' => ElFinder::ckeditorOptions('grom/media/manager', [
                        'filebrowserBrowseUrl' => ['/grom/menu/backend/item/ckeditor-select'],
                        'extraPlugins' => 'codesnippet',
                        'preset' => 'standard',
                        'tabSpaces' => 4
                    ])
                ]);
                break;
            case self::EDITOR_CKEDITOR_FULL:
                echo CKEditor::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'name' => $this->name,
                    'value' => $this->value,
                    'options' => ['class' => 'form-control', 'rows' => 10],
                    'editorOptions' => ElFinder::ckeditorOptions('grom/media/manager', [
                        'filebrowserBrowseUrl' => ['/grom/menu/backend/item/ckeditor-select'],
                        'extraPlugins' => 'codesnippet',
                        'preset' => 'full',
                        'tabSpaces' => 4
                    ])
                ]);
                break;
            case self::EDITOR_IMPERAVI:
                echo Widget::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'name' => $this->name,
                    'value' => $this->value,
                ]);
                break;
            default:
                echo $this->hasModel() ? Html::activeTextarea($this->model, $this->attribute, $this->options) : Html::textarea($this->name, $this->value, $this->options);
        }
    }

    public static function editorLabels()
    {
        return [
            self::EDITOR_CKEDITOR_BASIC => 'ElFinder basic',
            self::EDITOR_CKEDITOR_STANDARD => 'ElFinder standard',
            self::EDITOR_CKEDITOR_FULL => 'ElFinder full',
            self::EDITOR_IMPERAVI => 'Imperavi',
            self::EDITOR_TEXTAREA => 'Textarea',
        ];
    }
} 