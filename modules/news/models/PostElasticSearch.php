<?php
/**
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\news\models;

use gromver\platform\basic\elasticsearch\models\ActiveDocument;

/**
 * Class Post
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostElasticSearch extends ActiveDocument {
    public function attributes()
    {
        return ['id', 'alias', 'category_id', 'title', 'metakey', 'metadesc', 'published', 'language', 'tags', 'text', 'date'];
    }

    public static function model()
    {
        return Post::className();
    }

    /**
     * @param Post $model
     */
    public function loadModel($model)
    {
        $this->attributes = $model->toArray([], ['published', 'language', 'tags', 'text', 'date']);
    }

    public static function filter()
    {
        $filters = [
            [
                'not' => [
                    'and' => [
                        [
                            'type' => ['value' => 'post']
                        ],
                        [
                            'term' => ['published' => false]
                        ]
                    ]
                ]
            ],
        ];

        if ($unpublishedCategories = Category::find()->unpublished()->select('{{%grom_category}}.id')->column()) {
            $filters[] =             [
                'not' => [
                    'and' => [
                        [
                            'type' => ['value' => 'post']
                        ],
                        [
                            'term' => ['category_id' => $unpublishedCategories]
                        ]
                    ]
                ]
            ];
        }

        return $filters;
    }
} 