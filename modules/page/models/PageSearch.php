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
            [['id', 'parent_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock'], 'integer'],
            [['language', 'title', 'alias', 'path', 'preview_text', 'detail_text', 'metakey', 'metadesc', 'tags', 'versionNote'], 'safe'],
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
     * @param bool $withRoots
     *
     * @return ActiveDataProvider
     */
    public function search($params, $withRoots = false)
    {
        $query = $withRoots ? Page::find() : Page::find()->noRoots();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC
                ]
            ]
        ]);

        /*$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC]
            ]
        ]);*/

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%grom_page}}.id' => $this->id,
            '{{%grom_page}}.parent_id' => $this->parent_id,
            '{{%grom_page}}.created_at' => $this->created_at,
            '{{%grom_page}}.updated_at' => $this->updated_at,
            '{{%grom_page}}.status' => $this->status,
            '{{%grom_page}}.created_by' => $this->created_by,
            '{{%grom_page}}.updated_by' => $this->updated_by,
            '{{%grom_page}}.lft' => $this->lft,
            '{{%grom_page}}.rgt' => $this->rgt,
            '{{%grom_page}}.level' => $this->level,
            '{{%grom_page}}.ordering' => $this->ordering,
            '{{%grom_page}}.hits' => $this->hits,
            '{{%grom_page}}.lock' => $this->lock,
        ]);

        $query->andFilterWhere(['like', '{{%grom_page}}.language', $this->language])
            ->andFilterWhere(['like', '{{%grom_page}}.title', $this->title])
            ->andFilterWhere(['like', '{{%grom_page}}.path', $this->path])
            ->andFilterWhere(['like', '{{%grom_page}}.alias', $this->alias])
            ->andFilterWhere(['like', '{{%grom_page}}.preview_text', $this->preview_text])
            ->andFilterWhere(['like', '{{%grom_page}}.detail_text', $this->detail_text])
            ->andFilterWhere(['like', '{{%grom_page}}.metakey', $this->metakey])
            ->andFilterWhere(['like', '{{%grom_page}}.metadesc', $this->metadesc]);

        if($this->tags)
            $query->innerJoinWith('tags')->andFilterWhere(['{{%grom_tag}}.id' => $this->tags]);

        return $dataProvider;
    }
}
