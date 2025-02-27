<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Xmfmediospagos;

/**
 * XmfmediospagosSearch represents the model behind the search form of `backend\models\Xmfmediospagos`.
 */
class XmfmediospagosSearch extends Xmfmediospagos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idCabecera', 'cambio', 'monedaDolar', 'monedaLocal', 'CreditCard'], 'integer'],
            [['nro_recibo', 'documentoId', 'formaPago', 'numCheque', 'numComprobante', 'numTarjeta', 'bancoCode', 'fecha', 'centro', 'baucher', 'checkdate', 'transferencedate'], 'safe'],
            [['monto'], 'number'],
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
        $query = Xmfmediospagos::find();

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
            'idCabecera' => $this->idCabecera,
            'monto' => $this->monto,
            'fecha' => $this->fecha,
            'cambio' => $this->cambio,
            'monedaDolar' => $this->monedaDolar,
            'monedaLocal' => $this->monedaLocal,
            'CreditCard' => $this->CreditCard,
        ]);

        $query->andFilterWhere(['like', 'nro_recibo', $this->nro_recibo])
            ->andFilterWhere(['like', 'documentoId', $this->documentoId])
            ->andFilterWhere(['like', 'formaPago', $this->formaPago])
            ->andFilterWhere(['like', 'numCheque', $this->numCheque])
            ->andFilterWhere(['like', 'numComprobante', $this->numComprobante])
            ->andFilterWhere(['like', 'numTarjeta', $this->numTarjeta])
            ->andFilterWhere(['like', 'bancoCode', $this->bancoCode])
            ->andFilterWhere(['like', 'centro', $this->centro])
            ->andFilterWhere(['like', 'baucher', $this->baucher])
            ->andFilterWhere(['like', 'checkdate', $this->checkdate])
            ->andFilterWhere(['like', 'transferencedate', $this->transferencedate]);

        return $dataProvider;
    }
}
