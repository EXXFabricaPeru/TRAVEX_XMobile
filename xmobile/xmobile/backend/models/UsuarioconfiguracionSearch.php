<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Usuarioconfiguracion;

/**
 * UsuarioconfiguracionSearch represents the model behind the search form of `backend\models\Usuarioconfiguracion`.
 */
class UsuarioconfiguracionSearch extends Usuarioconfiguracion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idEstado', 'idTipoPrecio', 'estadoListaPrecio', 'idTipoImpresora', 'codEmpleadoVenta', 'idUser', 'modMoneda', 'estadoAlmacenes', 'crearCliente', 'territorio', 'grupoCliente', 'listaPrecios', 'descuentos', 'totalDescuentoDocumento', 'editarDocumento', 'aperturaCaja', 'cierreCaja'], 'integer'],
            [['ruta', 'ctaEfectivo', 'ctaCheque', 'ctaTransferencia', 'ctaFcEfectivo', 'ctaFcCheque', 'ctaFcTransferencia', 'sreOfertaVenta', 'sreOrdenVenta', 'sreFactura', 'sreFacturaReserva', 'sreCobro', 'modInfTributaria', 'codVendedor', 'nombre', 'almacenes', 'ctaTarjeta', 'ctaFcTarjeta', 'moneda', 'totalDescuento', 'condicionPago', 'ctaanticipo', 'multiListaPrecios','multiCamposUsuarios'], 'safe'],
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
        $query = Usuarioconfiguracion::find();

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
            'estadoListaPrecio' => $this->estadoListaPrecio,
            'idTipoImpresora' => $this->idTipoImpresora,
            'flujoCaja' => $this->flujoCaja,
            'codEmpleadoVenta' => $this->codEmpleadoVenta,
            'idUser' => $this->idUser,
            'modMoneda' => $this->modMoneda,
            'estadoAlmacenes' => $this->estadoAlmacenes,
            'crearCliente' => $this->crearCliente,
            'territorio' => $this->territorio,
            'grupoCliente' => $this->grupoCliente,
            'listaPrecios' => $this->listaPrecios,
            'descuentos' => $this->descuentos,
            'totalDescuentoDocumento' => $this->totalDescuentoDocumento,
            'editarDocumento' => $this->editarDocumento,
            'aperturaCaja' => $this->aperturaCaja,
            'cierreCaja' => $this->cierreCaja,
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
            ->andFilterWhere(['like', 'codVendedor', $this->codVendedor])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'almacenes', $this->almacenes])
            ->andFilterWhere(['like', 'ctaTarjeta', $this->ctaTarjeta])
            ->andFilterWhere(['like', 'ctaFcTarjeta', $this->ctaFcTarjeta])
            ->andFilterWhere(['like', 'moneda', $this->moneda])
            ->andFilterWhere(['like', 'totalDescuento', $this->totalDescuento])
            ->andFilterWhere(['like', 'condicionPago', $this->condicionPago])
            ->andFilterWhere(['like', 'ctaanticipo', $this->ctaanticipo])
            ->andFilterWhere(['like', 'multiListaPrecios', $this->multiListaPrecios])
            ->andFilterWhere(['like', 'multiCamposUsuarios', $this->multiCamposUsuarios]);

        return $dataProvider;
    }
}
