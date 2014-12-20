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
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class Translator
 * Вывоидт список трансляций для данной модели с сылками на просмотр либо создание трансляции
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Translator extends Widget {
    /**
     * @var ActiveRecord
     */
    public $model;
    public $languageParam = 'language';
    public $translationsParam = 'translations';
    public $updateRoute = 'update';
    public $createRoute = 'create';

    public function run()
    {
        $languages = [];
        $itemLanguage = $this->model->{$this->languageParam};
        $itemTranslations = $this->model->{$this->translationsParam};
        foreach(Yii::$app->languages as $lang)
            if($lang==$itemLanguage)
                $languages[] = Html::a($lang, [$this->updateRoute, 'id' => $this->model->id], ['class' => 'btn btn-primary btn-xs', 'data-pjax' => '0']);
            else if(isset($itemTranslations[$lang]))
                $languages[] = Html::a($lang, [$this->updateRoute, 'id' => $itemTranslations[$lang]->id], ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0']);
            else
                $languages[] = Html::a($lang, [$this->createRoute, 'language' => $lang, 'sourceId' => $this->model->id], ['class' => 'btn btn-danger btn-xs', 'data-pjax' => '0']);


        return implode(' ', $languages);
    }
} 