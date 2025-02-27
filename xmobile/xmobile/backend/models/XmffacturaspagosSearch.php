<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Xmffacturaspagos;

/**
 * XmffacturaspagosSearch represents the model behind the search form of `backend\models\Xmffacturaspagos`.
 */
class XmffacturaspagosSearch extends Xmffacturaspagos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idCabecera', 'cuota'], 'integer'],
            [['clienteId', 'nro_recibo', 'documentoId', 'docentry', 'CardName', 'nroFactura'], 'safe'],
            [['monto', 'saldo', 'DocTotal'], 'number'],
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
        $query = Xmffacturaspagos::find();

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
            'saldo' => $this->saldo,
            'DocTotal' => $this->DocTotal,
            'cuota' => $this->cuota,
        ]);

        $query->andFilterWhere(['like', 'clienteId', $this->clienteId])
            ->andFilterWhere(['like', 'nro_recibo', $this->nro_recibo])
            ->andFilterWhere(['like', 'documentoId', $this->documentoId])
            ->andFilterWhere(['like', 'docentry', $this->docentry])
            ->andFilterWhere(['like', 'CardName', $this->CardName])
            ->andFilterWhere(['like', 'nroFactura', $this->nroFactura]);

        return $dataProvider;
    }
}
