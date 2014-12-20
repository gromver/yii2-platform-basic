<?php
/**
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\news\models;

use gromver\platform\basic\modules\elasticsearch\models\ActiveDocument;

/**
 * Class Category
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategoryElasticSearch extends ActiveDocument {
    public function attributes()
    {
        return ['id', 'parent_id', 'title', 'metakey', 'metadesc', 'language', 'published', 'tags', 'text', 'date'];
    }

    public static function model()
    {
        return Category::className();
    }

    /**
     * @param Category $model
     */
    public function loadModel($model)
    {
        $this->attributes = $model->toArray([], ['published', 'tags', 'text', 'date']);
    }

    public static function filter()
    {
        $filters = [
            [
                'not' => [
                    'and' => [
                        [
                            'type' => ['value' => 'category']
                        ],
                        [
                            'term' => ['published' => false]
                        ]
                    ]
                ]
            ]
        ];

        if ($unpublishedCategories = Category::find()->unpublished()->select('{{%grom_category}}.id')->column()) {
            $filters[] = [
                'not' => [
                    'and' => [
                        [
                            'type' => ['value' => 'category']
                        ],
                        [
                            'term' => ['parent_id' => $unpublishedCategories]
                        ]
                    ]
                ]
            ];
        }

        return $filters;
    }
} 