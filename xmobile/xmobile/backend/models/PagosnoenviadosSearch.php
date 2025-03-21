<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Pagos;

/**
 * PagosSearch represents the model behind the search form of `backend\models\Pagos`.
 */
class PagosnoenviadosSearch extends Pagos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tipoCambioDolar','usuario','otpp','TransId'], 'integer'],
            [['documentoId', 'clienteId', 'recibo', 'formaPago', 'numCheque','equipoId', 'numComprobante', 'numTarjeta', 'numAhorro', 'numAutorizacion', 'bancoCode', 'ci', 'fecha', 'hora'], 'safe'],
            [['moneda', 'monto', 'cambio', 'monedaDolar'], 'number'],
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
        $query = Pagos::find()->where('estadoEnviado=0')->orderBy(['fecha' => SORT_DESC, 'hora' => SORT_DESC]);

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
            'tipoCambioDolar' => $this->tipoCambioDolar,
            'moneda' => $this->moneda,
            'monto' => $this->monto,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'cambio' => $this->cambio,
            'monedaDolar' => $this->monedaDolar,
        ]);

        $query->andFilterWhere(['like', 'documentoId', $this->documentoId])
            ->andFilterWhere(['like', 'clienteId', $this->clienteId])
            ->andFilterWhere(['like', 'formaPago', $this->formaPago])
            ->andFilterWhere(['like', 'numCheque', $this->numCheque])
            ->andFilterWhere(['like', 'numComprobante', $this->numComprobante])
            ->andFilterWhere(['like', 'numTarjeta', $this->numTarjeta])
            ->andFilterWhere(['like', 'numAhorro', $this->numAhorro])
            ->andFilterWhere(['like', 'numAutorizacion', $this->numAutorizacion])
            ->andFilterWhere(['like', 'bancoCode', $this->bancoCode])
            ->andFilterWhere(['like', 'equipoId', $this->equipoId])
			->andFilterWhere(['like', 'recibo', $this->recibo])
            ->andFilterWhere(['like', 'ci', $this->ci]);

        return $dataProvider;
    }
}
