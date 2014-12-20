<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\menu\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class MenuTypeSearch represents the model behind the search form about `gromver\platform\basic\common\models\Menu`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuTypeSearch extends MenuType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'lock'], 'integer'],
            [['title', 'alias', 'note'], 'safe'],
        ];
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
        $query = MenuType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            /*'sort' => [
                'defaultOrder' => ['ordering' => SORT_ASC]
            ]*/
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'lock' => $this->lock,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
