<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Permisosmiddle;

/**
 * PermisosmiddleSearch represents the model behind the search form of `backend\models\Permisosmiddle`.
 */
class PermisosmiddleSearch extends Permisosmiddle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idUsuario', 'nivel', 'idCargoEmpresa'], 'integer'],
            [['userName', 'descripcionNivel', 'departamento', 'cargoEmpresa', 'permisomenu'], 'safe'],
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
        $query = Permisosmiddle::find();

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
            'idUsuario' => $this->idUsuario,
            'nivel' => $this->nivel,
            'idCargoEmpresa' => $this->idCargoEmpresa,
        ]);

        $query->andFilterWhere(['like', 'userName', $this->userName])
            ->andFilterWhere(['like', 'descripcionNivel', $this->descripcionNivel])
            ->andFilterWhere(['like', 'departamento', $this->departamento])
            ->andFilterWhere(['like', 'cargoEmpresa', $this->cargoEmpresa])
            ->andFilterWhere(['like', 'permisomenu', $this->permisomenu]);

        return $dataProvider;
    }
}
