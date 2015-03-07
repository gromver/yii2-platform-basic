<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 07.03.15
 * Time: 12:45
 */

namespace gromver\platform\basic\widgets;


use gromver\models\SpecificationInterface;
use yii\widgets\InputWidget;

class HtmlEditorTrait extends InputWidget implements SpecificationInterface {
    use WidgetTrait;

    /**
     * @var string
     * @type list
     * @items editorLabels
     */
    public $editor;

    protected function launch()
    {
        echo 'HtmlEditorWidgetTRAIT coming soon!';
    }

    public static function editorLabels()
    {
        return [
            'fuck'
        ];
    }
} 