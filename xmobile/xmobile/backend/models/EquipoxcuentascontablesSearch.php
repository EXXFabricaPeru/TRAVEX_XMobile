<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Equipoxcuentascontables;
use Yii;

/**
 * EquipoxcuentascontablesSearch represents the model behind the search form of `backend\models\Equipoxcuentascontables`.
 */
class EquipoxcuentascontablesSearch extends Equipoxcuentascontables {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'equipoxId'], 'integer'],
            [['cuentaEfectivo', 'cuentaCheque', 'cuentaTranferencia', 'cuentaTarjeta', 'cuentaAnticipos', 'cuentaEfectivoUSD', 'cuentaChequeUSD', 'cuentaTranferenciaUSD', 'cuentaTarjetaUSD', 'cuentaAnticiposUSD'], 'safe'],
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
        $query = Equipoxcuentascontables::find();

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
            'equipoxId' => $this->equipoxId,
        ]);

        $query->andFilterWhere(['like', 'cuentaEfectivo', $this->cuentaEfectivo])
                ->andFilterWhere(['like', 'cuentaCheque', $this->cuentaCheque])
                ->andFilterWhere(['like', 'cuentaTranferencia', $this->cuentaTranferencia])
                ->andFilterWhere(['like', 'cuentaTarjeta', $this->cuentaTarjeta])
                ->andFilterWhere(['like', 'cuentaAnticipos', $this->cuentaAnticipos])
                ->andFilterWhere(['like', 'cuentaEfectivoUSD', $this->cuentaEfectivoUSD])
                ->andFilterWhere(['like', 'cuentaChequeUSD', $this->cuentaChequeUSD])
                ->andFilterWhere(['like', 'cuentaTranferenciaUSD', $this->cuentaTranferenciaUSD])
                ->andFilterWhere(['like', 'cuentaTarjetaUSD', $this->cuentaTarjetaUSD])
                ->andFilterWhere(['like', 'cuentaAnticiposUSD', $this->cuentaAnticiposUSD]);

        return $dataProvider;
    }

}
