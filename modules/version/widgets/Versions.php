<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\version\widgets;


use gromver\widgets\ModalIFrame;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * Class Versions
 * Кнопка с диалоговым окном истории версий для модели self::$model
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Versions extends \yii\base\Widget
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;
    /**
     * @var array
     */
    public $options = [
        'class'=>'btn btn-default'
    ];

    public function init()
    {
        if (!is_object($this->model)) {
            throw new InvalidConfigException(__CLASS__ . '::model must be set.');
        }
    }

    public function run()
    {
        return \gromver\widgets\ModalIFrame::widget([
            'options' => $this->options,
            'label' => '<i class="glyphicon glyphicon-hdd"></i> ' . Yii::t('gromver.platform', 'Versions'),
            'url' => ['/grom/version/backend/default/item', 'item_id' => $this->model->getPrimaryKey(), 'item_class' => $this->model->className()]
        ]);
    }
} 