<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\version\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class VersionSearch represents the model behind the search form about `gromver\platform\basic\modules\version\models\Version`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class VersionSearch extends Version
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'character_count', 'created_by'], 'integer'],
            [['item_class', 'version_note', 'version_hash', 'version_data', 'keep_forever'], 'safe'],
            [['created_at'], 'date', 'format' => 'dd.MM.yyyy', 'timestampAttribute' => 'created_at', 'when' => function() {
                    return is_string($this->created_at);
                }],
            [['created_at'], 'integer', 'enableClientValidation' => false],
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
        $query = Version::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->created_at) {
            $query->andWhere('{{%grom_version}}.created_at >= :timestamp', ['timestamp' => $this->created_at]);
        }

        $query->andFilterWhere([
            '{{%grom_version}}.id' => $this->id,
            '{{%grom_version}}.item_id' => $this->item_id,
            '{{%grom_version}}.character_count' => $this->character_count,
            '{{%grom_version}}.created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', '{{%grom_version}}.item_class', $this->item_class])
            ->andFilterWhere(['like', '{{%grom_version}}.version_note', $this->version_note])
            ->andFilterWhere(['like', '{{%grom_version}}.version_hash', $this->version_hash])
            ->andFilterWhere(['like', '{{%grom_version}}.version_data', $this->version_data])
            ->andFilterWhere(['like', '{{%grom_version}}.keep_forever', $this->keep_forever]);

        return $dataProvider;
    }
}
