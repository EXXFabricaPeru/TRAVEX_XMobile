<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Poligonocliente;

/**
 * PoligonoclienteSearch represents the model behind the search form of `backend\models\Poligonocliente`.
 */
class PoligonoclienteSearch extends Poligonocliente
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'territoryid', 'poligonoid', 'posicion', 'dia'], 'integer'],
            [['cardcode', 'cardname', 'latitud', 'longitud', 'territoryname', 'poligononombre','nombreDireccion','calle'], 'safe'],
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
        $query = Poligonocliente::find();

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
            'territoryid' => $this->territoryid,
            'poligonoid' => $this->poligonoid,
            'posicion' => $this->posicion,
            'dia' => $this->dia,
        ]);

        $query->andFilterWhere(['like', 'cardcode', $this->cardcode])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'latitud', $this->latitud])
            ->andFilterWhere(['like', 'longitud', $this->longitud])
            ->andFilterWhere(['like', 'territoryname', $this->territoryname])
            ->andFilterWhere(['like', 'poligononombre', $this->poligononombre]);

        return $dataProvider;
    }
}
