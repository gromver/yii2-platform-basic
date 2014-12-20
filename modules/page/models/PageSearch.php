<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class PageSearch represents the model behind the search form about `gromver\platform\basic\modules\page\models\Page`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageSearch extends Page
{
    public $tags;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'hits', 'lock'], 'integer'],
            [['language', 'title', 'alias', 'preview_text', 'detail_text', 'metakey', 'metadesc', 'tags', 'versionNote'], 'safe'],
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
        $query = Page::find();

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
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'hits' => $this->hits,
            'lock' => $this->lock,
        ]);

        $query->andFilterWhere(['like', '{{%grom_page}}.language', $this->language])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'preview_text', $this->preview_text])
            ->andFilterWhere(['like', 'detail_text', $this->detail_text])
            ->andFilterWhere(['like', 'metakey', $this->metakey])
            ->andFilterWhere(['like', 'metadesc', $this->metadesc]);

        if($this->tags)
            $query->innerJoinWith('tags')->andFilterWhere(['{{%grom_tag}}.id' => $this->tags]);

        return $dataProvider;
    }
}
