<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Motivonoventa;

/**
 * MotivonoventaSearch represents the model behind the search form of `backend\models\Motivonoventa`.
 */
class MotivonoventaSearch extends Motivonoventa
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'User', 'Status'], 'integer'],
            [['Code', 'Name', 'Razon', 'DateUpdate'], 'safe'],
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
        $query = Motivonoventa::find();

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
            'User' => $this->User,
            'Status' => $this->Status,
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Razon', $this->Razon]);

        return $dataProvider;
    }
}
