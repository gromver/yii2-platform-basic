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
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class PostSearch represents the model behind the search form about `gromver\platform\basic\modules\news\models\Post`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostSearch extends Post
{
    public $tags;
    public $language;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'ordering', 'hits', 'lock'], 'integer'],
            [['title', 'alias', 'preview_text', 'preview_image', 'detail_text', 'detail_image', 'metakey', 'metadesc', 'tags', 'versionNote', 'language'], 'safe'],
            [['published_at'], 'date', 'format' => 'dd.MM.yyyy', 'timestampAttribute' => 'published_at', 'when' => function() {
                    return is_string($this->published_at);
                }],
            [['published_at'], 'integer', 'enableClientValidation' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%grom_post}}.id' => $this->id,
            '{{%grom_post}}.category_id' => $this->category_id,
            '{{%grom_post}}.created_at' => $this->created_at,
            '{{%grom_post}}.updated_at' => $this->updated_at,
            '{{%grom_post}}.status' => $this->status,
            '{{%grom_post}}.created_by' => $this->created_by,
            '{{%grom_post}}.updated_by' => $this->updated_by,
            '{{%grom_post}}.ordering' => $this->ordering,
            '{{%grom_post}}.hits' => $this->hits,
            '{{%grom_post}}.lock' => $this->lock,
        ]);

        if ($this->published_at) {
            $query->andWhere('{{%grom_post}}.published_at >= :timestamp', ['timestamp' => $this->published_at]);
        }

        $query->andFilterWhere(['like', '{{%grom_post}}.title', $this->title])
            ->andFilterWhere(['like', '{{%grom_post}}.alias', $this->alias])
            ->andFilterWhere(['like', '{{%grom_post}}.preview_text', $this->preview_text])
            ->andFilterWhere(['like', '{{%grom_post}}.preview_image', $this->preview_image])
            ->andFilterWhere(['like', '{{%grom_post}}.detail_text', $this->detail_text])
            ->andFilterWhere(['like', '{{%grom_post}}.detail_image', $this->detail_image])
            ->andFilterWhere(['like', '{{%grom_post}}.metakey', $this->metakey])
            ->andFilterWhere(['like', '{{%grom_post}}.metadesc', $this->metadesc]);

        if($this->tags)
            $query->innerJoinWith('tags')->andFilterWhere(['{{%grom_tag}}.id' => $this->tags]);

        if($this->language)
            $query->innerJoinWith('category', false)->andFilterWhere(['like', '{{%grom_category}}.language', $this->language]);


        return $dataProvider;
    }
}
