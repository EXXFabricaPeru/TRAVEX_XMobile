<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Clientesgrupo;

/**
 * ClientesgrupoSearch represents the model behind the search form of `backend\models\Clientesgrupo`.
 */
class ClientesgrupoSearch extends Clientesgrupo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'User'], 'integer'],
            [['Code', 'Name', 'Type', 'Status', 'DateUpdate'], 'safe'],
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
        $query = Clientesgrupo::find();

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
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Type', $this->Type])
            ->andFilterWhere(['like', 'Status', $this->Status]);

        return $dataProvider;
    }
}
