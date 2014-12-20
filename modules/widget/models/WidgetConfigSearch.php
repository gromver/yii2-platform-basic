<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widget\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class WidgetConfigSearch
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class WidgetConfigSearch extends WidgetConfig
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'valid', 'created_at', 'updated_at', 'created_by', 'updated_by', 'lock'], 'integer'],
            [['widget_id', 'widget_class', 'context', 'url', 'params'], 'safe'],
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
        $query = WidgetConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'valid' => $this->valid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'lock' => $this->lock,
        ]);

        $query->andFilterWhere(['like', 'widget_id', $this->widget_id])
            ->andFilterWhere(['like', 'widget_class', $this->widget_class])
            ->andFilterWhere(['like', 'context', $this->context])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'params', $this->params]);

        return $dataProvider;
    }
}
