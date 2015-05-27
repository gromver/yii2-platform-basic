<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\platform\basic\modules\widget\widgets\Widget;
use yii\helpers\Html;

/**
 * Class HtmlBlock
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class HtmlBlock extends Widget
{
    /**
     * @translation gromver.platform
     */
    public $title;
    /**
     * @field editor
     * @label Content
     * @translation gromver.platform
     * @var string
     */
    public $html;

    protected function launch()
    {
        if (!empty($this->title)) {
            echo Html::tag('h3', $this->title);
        }

        echo $this->html;
    }
} 