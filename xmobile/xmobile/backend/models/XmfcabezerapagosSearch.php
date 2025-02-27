<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Xmfcabezerapagos;

/**
 * XmfcabezerapagosSearch represents the model behind the search form of `backend\models\Xmfcabezerapagos`.
 */
class XmfcabezerapagosSearch extends Xmfcabezerapagos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'correlativo', 'usuario', 'otpp', 'estado', 'cancelado', 'equipo', 'TransId', 'idDocumento'], 'integer'],
            [['nro_recibo', 'documentoId', 'fecha', 'hora', 'tipo', 'moneda', 'cliente_carcode', 'razon_social', 'nit', 'tipoTarjeta', 'fechaSistema', 'latitud', 'longitud'], 'safe'],
            [['monto_total', 'tipo_cambio'], 'number'],
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
        $query = Xmfcabezerapagos::find()->orderBy('id desc');;

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
            'correlativo' => $this->correlativo,
            'usuario' => $this->usuario,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'monto_total' => $this->monto_total,
            'otpp' => $this->otpp,
            'tipo_cambio' => $this->tipo_cambio,
            'estado' => $this->estado,
            'cancelado' => $this->cancelado,
            'equipo' => $this->equipo,
            'fechaSistema' => $this->fechaSistema,
            'TransId' => $this->TransId,
            'idDocumento' => $this->idDocumento,
        ]);

        $query->andFilterWhere(['like', 'nro_recibo', $this->nro_recibo])
            ->andFilterWhere(['like', 'documentoId', $this->documentoId])
            ->andFilterWhere(['like', 'tipo', $this->tipo])
            ->andFilterWhere(['like', 'moneda', $this->moneda])
            ->andFilterWhere(['like', 'cliente_carcode', $this->cliente_carcode])
            ->andFilterWhere(['like', 'razon_social', $this->razon_social])
            ->andFilterWhere(['like', 'nit', $this->nit])
            ->andFilterWhere(['like', 'tipoTarjeta', $this->tipoTarjeta])
            ->andFilterWhere(['like', 'latitud', $this->latitud])
            ->andFilterWhere(['like', 'longitud', $this->longitud]);

        return $dataProvider;
    }
}
