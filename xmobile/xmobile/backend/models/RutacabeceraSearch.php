<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Rutacabecera;
use Yii;

/**
 * RutacabeceraSearch represents the model behind the search form of `backend\models\Rutacabecera`.
 */
class RutacabeceraSearch extends Rutacabecera {
     
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'idvendedor', 'idclienteinicial', 'usuario', 'status'], 'integer'],
            [['nombre', 'idvendedor', 'fecha','latitud','longitud','vendedor'], 'safe'],
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
        $query = Rutacabecera::find()->orderby('id desc');

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
            'nombre' => $this->nombre,
            'idvendedor' => $this->idvendedor,
            'fecha' => $this->fecha,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'nombre', $this->nombre])
                ->andFilterWhere(['like', 'idvendedor', $this->idvendedor])
                ->andFilterWhere(['like', 'latitud', $this->idvendedor])
                ->andFilterWhere(['like', 'longitud', $this->idvendedor])
                ->andFilterWhere(['like', 'fecha', $this->fecha]);

        return $dataProvider;
    }
}