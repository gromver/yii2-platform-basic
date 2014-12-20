<?php
/**
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page\models;

use gromver\platform\basic\modules\elasticsearch\models\ActiveDocument;

/**
 * Class Page
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageElasticSearch extends ActiveDocument {
    public function attributes()
    {
        return ['id', 'title', 'alias', 'metakey', 'metadesc', 'language', 'published', 'tags', 'text', 'date'];
    }

    public static function model()
    {
        return Page::className();
    }

    /**
     * @param Page $model
     */
    public function loadModel($model)
    {
        $this->attributes = $model->toArray([], ['published', 'tags', 'text', 'date']);
    }

    public static function filter()
    {
        return [
            [
                'not' => [
                    'and' => [
                        [
                            'type' => ['value' => 'page']
                        ],
                        [
                            'term' => ['published' => false]
                        ]
                    ]
                ]
            ]
        ];
    }
} 