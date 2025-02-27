<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Geolocalizacion;
use Yii;

/**
 * GeolocalizacionSearch represents the model behind the search form of `backend\models\Geolocalizacion`.
 */
class GeolocalizacionSearch extends Geolocalizacion {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'idequipox', 'idcliente', 'estado', 'usuario', 'status'], 'integer'],
            [['latitud', 'longitud', 'fecha', 'hora', 'documentocod', 'tipodoc', 'estado', 'actividad', 'anexo'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Geolocalizacion::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'query' => $query->andFilterWhere(['equipoId' => Yii::$app->session->get('IDEQUIPO')]),
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
            'idequipox' => $this->idequipox,
            'longitud' => $this->longitud,
            'latitud' => $this->latitud,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'idcliente' => $this->idcliente,
            'documentocod' => $this->documentocod,
            'tipodoc' => $this->tipodoc,
            'estado' => $this->estado,
            'actividad' => $this->actividad,
            'anexo' => $this->anexo,
            'usuario' => $this->usuario,
            'status' => $this->status,
            'dateUpdate' => $this->dateUpdate
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'idequipox', $this->idequipox])
                ->andFilterWhere(['like', 'longitud', $this->longitud])
                ->andFilterWhere(['like', 'latitud', $this->latitud])
                ->andFilterWhere(['like', 'fecha', $this->fecha])
                ->andFilterWhere(['like', 'hora', $this->hora])
                ->andFilterWhere(['like', 'idcliente', $this->idcliente])
                ->andFilterWhere(['like', 'documentocod', $this->documentocod])
                ->andFilterWhere(['like', 'tipodoc', $this->tipodoc])
                ->andFilterWhere(['like', 'estado', $this->estado])
                ->andFilterWhere(['like', 'actividad', $this->actividad])
                ->andFilterWhere(['like', 'anexo', $this->anexo])
                ->andFilterWhere(['like', 'usuario', $this->usuario]);

        return $dataProvider;
    }

}
