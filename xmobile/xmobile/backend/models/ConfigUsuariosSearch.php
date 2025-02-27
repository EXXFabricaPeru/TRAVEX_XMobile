<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ConfigUsuarios;

/**
 * ConfigUsuariosSearch represents the model behind the search form of `backend\models\ConfigUsuarios`.
 */
class ConfigUsuariosSearch extends ConfigUsuarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idEstado', 'idTipoPrecio', 'idTipoImpresora'], 'integer'],
            [['ruta', 'ctaEfectivo', 'ctaCheque', 'ctaTransferencia', 'ctaFcEfectivo', 'ctaFcCheque', 'ctaFcTransferencia', 'sreOfertaVenta', 'sreOrdenVenta', 'sreFactura', 'sreFacturaReserva', 'sreCobro', 'modInfTributaria', 'codEmpleadoVenta', 'codVendedor', 'nombre'], 'safe'],
            [['flujoCaja'], 'number'],
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
        $query = ConfigUsuarios::find();

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
            'idEstado' => $this->idEstado,
            'idTipoPrecio' => $this->idTipoPrecio,
            'idTipoImpresora' => $this->idTipoImpresora,
            'flujoCaja' => $this->flujoCaja,
        ]);

        $query->andFilterWhere(['like', 'ruta', $this->ruta])
            ->andFilterWhere(['like', 'ctaEfectivo', $this->ctaEfectivo])
            ->andFilterWhere(['like', 'ctaCheque', $this->ctaCheque])
            ->andFilterWhere(['like', 'ctaTransferencia', $this->ctaTransferencia])
            ->andFilterWhere(['like', 'ctaFcEfectivo', $this->ctaFcEfectivo])
            ->andFilterWhere(['like', 'ctaFcCheque', $this->ctaFcCheque])
            ->andFilterWhere(['like', 'ctaFcTransferencia', $this->ctaFcTransferencia])
            ->andFilterWhere(['like', 'sreOfertaVenta', $this->sreOfertaVenta])
            ->andFilterWhere(['like', 'sreOrdenVenta', $this->sreOrdenVenta])
            ->andFilterWhere(['like', 'sreFactura', $this->sreFactura])
            ->andFilterWhere(['like', 'sreFacturaReserva', $this->sreFacturaReserva])
            ->andFilterWhere(['like', 'sreCobro', $this->sreCobro])
            ->andFilterWhere(['like', 'modInfTributaria', $this->modInfTributaria])
            ->andFilterWhere(['like', 'codEmpleadoVenta', $this->codEmpleadoVenta])
            ->andFilterWhere(['like', 'codVendedor', $this->codVendedor])
            ->andFilterWhere(['like', 'nombre', $this->nombre]);

        return $dataProvider;
    }
}
