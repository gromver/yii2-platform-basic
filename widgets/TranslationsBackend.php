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
use yii\db\ActiveRecord;
use yii\helpers\Html;
use gromver\platform\basic\interfaces\model\TranslatableInterface;

/**
 * Class TranslationsBackend
 * TranslationsBackend используется в CRUD контроллерах для отображения списка локализаций указанной модели.
 * Модель должна поддерживать gromver\platform\basic\interfaces\model\TranslatableInterface
 * В список попадают все локализации, относящиеся к модели, а также те локализации,
 * которые поддерживает приложение, но не попали в список.
 * Список сортируется по алфавиту.
 * Существующие локализации ведут к path/to/controller/update?id=1
 * Не существующие локализации ведут к path/to/controller/create?sourceId=1&language=en
 *
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TranslationsBackend extends \yii\base\Widget
{
    /**
     * @var ActiveRecord | TranslatableInterface
     */
    public $model;

    public function run()
    {
        $buttons = [];

        $translations = $this->model->translations;
        foreach ($translations as $translationModel) {
            /** @var ActiveRecord | TranslatableInterface $translationModel */
            // if ($translationModel->equals($this->model)) continue;
            $lang = $translationModel->language;
            $buttons[$lang] = Html::a($lang, ['update', 'id' => $translationModel->getPrimaryKey()], ['class' => 'btn btn-xs' . ($this->model->language == $lang ? ' btn-primary' : ' btn-default'), 'data-pjax' => '0']);
        }

        $unsupportedLanguages = array_diff(Yii::$app->acceptedLanguages, array_keys($buttons));
        foreach ($unsupportedLanguages as $lang) {
            $buttons[$lang] = Html::a($lang, ['create', 'language' => $lang, 'sourceId' => $this->model->getPrimaryKey()], ['class' => 'btn btn-danger btn-xs', 'data-pjax' => '0']);
        }

        ksort($buttons);

        return implode(' ', $buttons);
    }
} 