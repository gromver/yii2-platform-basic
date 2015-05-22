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

    public function init()
    {
        if (!is_object($this->model)) {
            throw new InvalidConfigException(__CLASS__ . '::model must be set.');
        }
    }

    public function run()
    {
        return ModalIFrame::widget([
            'modalOptions' => [
                'header' => Yii::t('gromver.platform', 'Item Versions Manager - "{title}" (ID:{id})', ['title' => $this->model->title, 'id' => $this->model->getPrimaryKey()]),  //todo возможно ввести какойнибуть интерфейс для тайтла
                'size' => Modal::SIZE_LARGE,
            ],
            'buttonContent' => Html::a('<i class="glyphicon glyphicon-hdd"></i> ' . Yii::t('gromver.platform', 'Versions'),
                ['/grom/version/backend/default/item', 'item_id' => $this->model->getPrimaryKey(), 'item_class' => $this->model->className()], [
                    'class'=>'btn btn-default btn-sm',
                ]),
        ]);
    }
} 