<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Usuariolog;

/**
 * UsuariologSearch represents the model behind the search form of `backend\models\Usuariolog`.
 */
class UsuariologSearch extends Usuariolog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','idUsuario'], 'integer'],
            [['fechaIngreso','fecha', 'usuario', 'ipAddress', 'codigo'], 'safe'],
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
        $query = Usuariolog::find();

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
            'fecha' => $this->fecha,
            'fechaIngreso' => $this->fechaIngreso,
        ]);

        $query->andFilterWhere(['like', 'usuario', $this->usuario])
            ->andFilterWhere(['like', 'ipAddress', $this->ipAddress])
            ->andFilterWhere(['like', 'codigo', $this->codigo]);

        return $dataProvider;
    }
}
