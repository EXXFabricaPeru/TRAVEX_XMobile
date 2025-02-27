<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Sucursalx;

/**
 * SucursalxSearch represents the model behind the search form of `backend\models\Sucursalx`.
 */
class SucursalxSearch extends Sucursalx
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nombre', 'direccion', 'descripcion', 'codigo', 'empresa', 'telefonouno', 'telefonodos', 'pais', 'ciudad', 'leyendauno', 'leyendados'], 'safe'],
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
        $query = Sucursalx::find();

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
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'empresa', $this->empresa])
            ->andFilterWhere(['like', 'telefonouno', $this->telefonouno])
            ->andFilterWhere(['like', 'telefonodos', $this->telefonodos])
            ->andFilterWhere(['like', 'pais', $this->pais])
            ->andFilterWhere(['like', 'ciudad', $this->ciudad])
            ->andFilterWhere(['like', 'leyendauno', $this->leyendauno])
            ->andFilterWhere(['like', 'leyendados', $this->leyendados]);

        return $dataProvider;
    }
}
