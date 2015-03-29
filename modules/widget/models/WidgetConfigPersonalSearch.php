<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\widget\models;


use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class WidgetConfigSearch
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class WidgetConfigPersonalSearch extends WidgetConfigPersonal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'valid', 'created_at', 'updated_at', 'updated_by', 'lock'], 'integer'],
            [['widget_id', 'widget_class', 'context', 'url', 'params', 'language', 'created_by'], 'safe'],
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
        $query = WidgetConfigPersonal::find()->with('owner');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%grom_widget_config_personal}}.id' => $this->id,
            '{{%grom_widget_config_personal}}.valid' => $this->valid,
            '{{%grom_widget_config_personal}}.created_at' => $this->created_at,
            '{{%grom_widget_config_personal}}.updated_at' => $this->updated_at,
            //'{{%grom_widget_config_personal}}.created_by' => $this->created_by,
            '{{%grom_widget_config_personal}}.updated_by' => $this->updated_by,
            '{{%grom_widget_config_personal}}.lock' => $this->lock,
            '{{%grom_widget_config_personal}}.language' => $this->language,
        ]);

        $query->andFilterWhere(['like', '{{%grom_widget_config_personal}}.widget_id', $this->widget_id])
            ->andFilterWhere(['like', '{{%grom_widget_config_personal}}.widget_class', $this->widget_class])
            ->andFilterWhere(['like', '{{%grom_widget_config_personal}}.context', $this->context])
            ->andFilterWhere(['like', '{{%grom_widget_config_personal}}.url', $this->url])
            ->andFilterWhere(['like', '{{%grom_widget_config_personal}}.params', $this->params]);

        if ($this->created_by) {
            $query->joinWith('owner')->andWhere(['or', ['{{%grom_widget_config_personal}}.created_by' => $this->created_by], ['like', '{{%grom_user}}.username', $this->created_by]]);
        }

        return $dataProvider;
    }
}
