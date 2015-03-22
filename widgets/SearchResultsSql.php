<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\modulequery\ModuleEvent;
use gromver\platform\basic\modules\sqlsearch\models\Index;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * Class SearchResultsSql
 * Виджет отображающий результаты sql-поиска по запросу пользователя.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchResultsSql extends Widget
{
    const EVENT_BEFORE_SEARCH = 'SqlBeforeSearch_';

    /**
     * Модели по которым будет производится поиск
     * @ignore
     * @var string[]
     */
    public $models;
    /**
     * Поисковый запрос пользователя
     * @var string
     */
    public $query;
    /**
     * Определяет какие использовать ссылки: на фронт или бэкенд
     * @ignore
     * @var string
     */
    public $frontendMode = true;
    /**
     * @field list
     * @items layouts
     * @editable
     */
    public $layout = 'searchSql/results';
    /**
     * @field list
     * @items itemLayouts
     * @editable
     */
    public $itemLayout = '_itemDefault';
    public $pageSize = 10;
    /**
     * Количество символов в фрагменте хайлайта
     * @var int
     */
    public $fragmentSize = 200;
    /**
     * Разделитель фрагментов
     * @var string
     */
    public $fragmentDelimiter = ' ... ';
    /**
     * Шаблон подсветки поисковых слов
     * @var string
     * @ignore
     */
    public $highlightTemplate = '<span class="search-word">$0</span>';

    /**
     * Массив слов по которым будет производится поиск. Массив формируется из запроса пользователя:
     * запрос разбивается на слова, при этом отфильтровываются слова длинна которых менее 4 символов
     * @var array
     */
    private $_words;

    public function init()
    {
        $words = preg_split('/[\s,]+/', trim($this->query), -1, PREG_SPLIT_NO_EMPTY);
        $this->_words = array_filter($words, [$this, 'filterWord']);
    }

    protected function filterWord($value)
    {
        return mb_strlen($value) >= 4;
    }

    protected function launch()
    {
        $query = Index::find();

        $indexTable = Index::tableName();

        if (!empty($this->_words)) {
            $searchWords = Yii::$app->db->quoteValue(implode(' ', $this->_words));
            $query->select([
                "{$indexTable}.*",
                'relevance' => "(MATCH ({$indexTable}.title, {$indexTable}.content) AGAINST ({$searchWords}) + (MATCH ({$indexTable}.tags) AGAINST ({$searchWords}) * 0.3))"
            ])
                ->where("MATCH ({$indexTable}.title, {$indexTable}.content) AGAINST ({$searchWords}) OR MATCH ({$indexTable}.tags) AGAINST ({$searchWords})")
                ->orderBy("relevance DESC");
        } else {
            // empty search
            $query->select("{$indexTable}.*")->orderBy("{$indexTable}.updated_at DESC");
        }

        if ($this->models) {
            // ищем только по тем моделям которые в списке
            $query->andWhere(['model_class' => $this->models]);
            // ивент на модификацию запроса сторонними модулями
            foreach ($this->models as $modelClass) {
                ModuleEvent::trigger(self::EVENT_BEFORE_SEARCH . $modelClass, [$query, $this]);
            }
        }

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => $this->pageSize
                    ]
                ]),
            'itemLayout' => $this->itemLayout
        ]);
    }

    /**
     * Вырезает релевантный запросу кусок текста и подсвечивает поисковые слова
     * @param $text
     * @return mixed
     */
    public function highlightText($text)
    {
        $positions = [];
        $margin = $this->fragmentSize / 2;
        foreach ($this->_words as $word) {
            if (($pos = mb_stripos($text, $word, null, 'UTF-8')) !== false) {
                $positions[] = max($pos - $margin, 0);
            }
        }
        if (empty($positions)) {
            return mb_substr($text, 0, $this->fragmentSize, 'UTF-8');
        } else {
            sort($positions);
            $fragment = '';
            while (($pos = array_shift($positions)) !== null) {
                $size = $this->fragmentSize;
                while (is_int($nextPos = @$positions[0]) && $nextPos >= $pos && $nextPos <= ($pos + $size)) {
                    $size += $nextPos - $pos;
                    array_shift($positions);
                }
                $fragment .= ($pos ? $this->fragmentDelimiter : '') . mb_substr($text, $pos, $size, 'UTF-8');
            }
            if ($pos + $size <= mb_strlen($text)) {
                $fragment .= $this->fragmentDelimiter;
            }

            return preg_replace('/\w*('. implode('|', $this->_words) . ')\w*/ui', $this->highlightTemplate, $fragment);
        }
    }

    public static function layouts()
    {
        return [
            'searchSql/results' => Yii::t('gromver.platform', 'Default'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemDefault' => Yii::t('gromver.platform', 'Default'),
        ];
    }
}