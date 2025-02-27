<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Camposusuarios;

/**
 * CamposUsuariosSearch represents the model behind the search form of `backend\models\Camposusuarios`.
 */
class CamposUsuariosSearch extends Camposusuarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'tipocampo', 'longitud', 'Status'], 'integer'],
            [['Objeto', 'Nombre', 'Tblsap', 'Campsap', 'Fechainsert', 'Userinser', 'FechaUpdate', 'UserUpdate','Campmidd'], 'safe'],
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
        $query = Camposusuarios::find();

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
            'tipocampo' => $this->tipocampo,
            'longitud' => $this->longitud,
            'Fechainsert' => $this->Fechainsert,
            'FechaUpdate' => $this->FechaUpdate,
            'Status' => $this->Status,
        ]);

        $query->andFilterWhere(['like', 'Objeto', $this->Objeto])
            ->andFilterWhere(['like', 'Nombre', $this->Nombre])
            ->andFilterWhere(['like', 'Tblsap', $this->Tblsap])
            ->andFilterWhere(['like', 'Campsap', $this->Campsap])
            ->andFilterWhere(['like', 'Userinser', $this->Userinser])
            ->andFilterWhere(['like', 'UserUpdate', $this->UserUpdate]);

        return $dataProvider;
    }
}
