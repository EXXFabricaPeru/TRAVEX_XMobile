<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PuntoVenta;
use Yii;

/**
 * PuntoVentaSearch represents the model behind the search form of `backend\models\PuntoVenta`.
 */
class PuntoVentaSearch extends PuntoVenta {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'fexcompany', 'idpuntoventa'], 'integer'],
            [['descripcion'], 'string'],
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
        $query = PuntoVenta::find();

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
            'fexcompany' => $this->DocEntry,
            'idpuntoventa' => $this->UserSign,
        ]);

        $query->andFilterWhere(['like', 'fexcompany', $this->fexcompany])
                ->andFilterWhere(['like', 'idpuntoventa', $this->idpuntoventa]);

        return $dataProvider;
    }

}
