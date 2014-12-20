<?php
/**
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\elasticsearch\models;

/**
 * Class Search
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ElasticSearch extends ActiveDocument {
    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        return ($documentClass = ActiveDocument::findDocumentByType($row['_type'])) ? new $documentClass : new static;
    }

    public static function type()
    {
        return '';
    }

    public function attributes()
    {
        return ['title', 'text', 'date'];
    }

    // ViewableInterface fallback
    public function getFrontendViewLink()
    {
        return [''];
    }

    public static function frontendViewLink($model)
    {
        return [''];
    }

    public function getBackendViewLink()
    {
        return [''];
    }

    public static function backendViewLink($model)
    {
        return [''];
    }
}