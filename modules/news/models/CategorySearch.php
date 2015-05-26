<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\news\models;


use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class CategorySearch represents the model behind the search form about `gromver\platform\basic\modules\news\models\Category`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategorySearch extends Category
{
    /**
     * @var integer[]
     */
    public $tags;
    /**
     * @var string
     */
    public $published_at_to;
    /**
     * @var integer
     */
    public $published_at_to_timestamp;
    /**
     * @var integer
     */
    public $published_at_timestamp;
    /**
     * @var bool
     */
    public $excludeRoots = true;
    /**
     * @var integer
     */
    public $excludeCategory;

    public function rules()
    {
        return [
            [['id', 'parent_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock', 'excludeCategory'], 'integer'],
            [['language', 'title', 'alias', 'path', 'preview_text', 'preview_image', 'detail_text', 'detail_image', 'metakey', 'metadesc', 'tags', 'versionNote'], 'safe'],
            [['published_at'], 'date', 'format' => 'dd.MM.yyyy', 'timestampAttribute' => 'published_at_timestamp'],
            [['published_at_to'], 'date', 'format' => 'dd.MM.yyyy', 'timestampAttribute' => 'published_at_to_timestamp'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Category::find();

        if ($this->excludeRoots) {
            $query->excludeRoots();
        }

        $query->with(['tags', 'translations', 'parent']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%grom_category}}.id' => $this->id,
            '{{%grom_category}}.parent_id' => $this->parent_id,
            '{{%grom_category}}.created_at' => $this->created_at,
            '{{%grom_category}}.updated_at' => $this->updated_at,
            '{{%grom_category}}.status' => $this->status,
            '{{%grom_category}}.created_by' => $this->created_by,
            '{{%grom_category}}.updated_by' => $this->updated_by,
            '{{%grom_category}}.lft' => $this->lft,
            '{{%grom_category}}.rgt' => $this->rgt,
            '{{%grom_category}}.level' => $this->level,
            '{{%grom_category}}.ordering' => $this->ordering,
            '{{%grom_category}}.hits' => $this->hits,
            '{{%grom_category}}.lock' => $this->lock,
        ]);

        if ($this->published_at_timestamp) {
            $query->andWhere('{{%grom_category}}.published_at >= :timestamp_from', ['timestamp_from' => $this->published_at_timestamp]);
        }
        if ($this->published_at_to_timestamp) {
            $query->andWhere('{{%grom_category}}.published_at <= :timestamp_to', ['timestamp_to' => $this->published_at_to_timestamp]);
        }

        $query->andFilterWhere(['like', '{{%grom_category}}.language', $this->language])
            ->andFilterWhere(['like', '{{%grom_category}}.title', $this->title])
            ->andFilterWhere(['like', '{{%grom_category}}.alias', $this->alias])
            ->andFilterWhere(['like', '{{%grom_category}}.path', $this->path])
            ->andFilterWhere(['like', '{{%grom_category}}.preview_text', $this->preview_text])
            ->andFilterWhere(['like', '{{%grom_category}}.preview_image', $this->preview_image])
            ->andFilterWhere(['like', '{{%grom_category}}.detail_text', $this->detail_text])
            ->andFilterWhere(['like', '{{%grom_category}}.detail_image', $this->detail_image])
            ->andFilterWhere(['like', '{{%grom_category}}.metakey', $this->metakey])
            ->andFilterWhere(['like', '{{%grom_category}}.metadesc', $this->metadesc]);

        if ($this->excludeCategory && $category = Category::findOne($this->excludeCategory)) {
            /** @var $category Category */
            $query->excludeCategory($category);
        }

        if($this->tags) {
            $query->innerJoinWith('tags')->andFilterWhere(['{{%grom_tag}}.id' => $this->tags]);
        }

        return $dataProvider;
    }
}
