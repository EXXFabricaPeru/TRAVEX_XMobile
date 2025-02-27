<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Clientesterritorio;

/**
 * ClientesterritorioSearch represents the model behind the search form of `backend\models\Clientesterritorio`.
 */
class ClientesterritorioSearch extends Clientesterritorio
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'TerritoryId'], 'integer'],
            [['CardCode', 'CardName', 'TerritoryName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Clientesterritorio::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'TerritoryId' => $this->TerritoryId,
        ]);

        $query->andFilterWhere(['like', 'CardCode', $this->CardCode])
            ->andFilterWhere(['like', 'CardName', $this->CardName])
            ->andFilterWhere(['like', 'TerritoryName', $this->TerritoryName]);

        return $dataProvider;
    }
}
