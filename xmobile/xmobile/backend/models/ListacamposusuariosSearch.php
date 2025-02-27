<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Listacamposusuarios;

/**
 * ListacamposusuariosSearch represents the model behind the search form of `backend\models\Listacamposusuarios`.
 */
class ListacamposusuariosSearch extends Listacamposusuarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdcampoUsuario', 'Status'], 'integer'],
            [['codigo', 'nombre'], 'safe'],
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
        $query = Listacamposusuarios::find();

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
            'Id' => $this->Id,
            'IdcampoUsuario' => $this->IdcampoUsuario,
            'Status' => $this->Status,
        ]);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'nombre', $this->nombre]);

        return $dataProvider;
    }
}
