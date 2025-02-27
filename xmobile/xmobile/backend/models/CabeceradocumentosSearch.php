<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Cabeceradocumentos;

/**
 * CabeceradocumentosSearch represents the model behind the search form of `backend\models\Cabeceradocumentos`.
 */
class CabeceradocumentosSearch extends Cabeceradocumentos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DocEntry', 'DocType', 'canceled', 'Printed', 'DocStatus', 'DocDate', 'DocDueDate', 'CardCode', 'CardName', 'NumAtCard', 'DocCur', 'Ref1', 'Ref2', 'Comments', 'JrnlMemo', 'TaxDate', 'LicTradNum', 'Address', 'CreateDate', 'UpdateDate', 'U_4NIT', 'U_4RAZON_SOCIAL', 'U_LATITUD', 'U_LONGITUD', 'U_4DOCUMENTOORIGEN', 'U_4MIGRADOCONCEPTO', 'U_4MIGRADO', 'fecharegistro', 'fechaupdate', 'fechasend', 'idDocPedido', 'rowNum', 'PayTermsGrpCode', 'UNumFactura', 'ControlCode', 'Indicator', 'ShipToCode', 'ControlAccount', 'U_LB_NumeroFactura', 'U_LB_EstadoFactura', 'U_LB_NumeroAutorizac', 'clone', 'giftcard'], 'safe'],
            [['DocNum', 'DocRate', 'PaidToDate', 'GroupNum', 'SlpCode', 'Series', 'UserSign', 'UserSign2', 'U_4MOTIVOCANCELADO', 'U_4SUBTOTAL', 'PriceListNum', 'estadosend', 'id', 'idUser', 'estado', 'gestion', 'mes', 'correlativo', 'DocNumSAP', 'actsl', 'U_LB_TipoFactura', 'U_LB_TotalNCND', 'Reserve'], 'integer'],
            [['DiscPrcnt', 'DiscSum', 'DocTotal', 'DocTotalPay', 'TotalDiscMonetary', 'TotalDiscPrcnt'], 'number'],
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
        $query = Cabeceradocumentos::find()->orderBy('id DESC');
        //$query = Cabeceradocumentos::find()->orderBy('DocDate DESC, id ASC');

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
            'DocNum' => $this->DocNum,
            'DocDate' => $this->DocDate,
            'DocDueDate' => $this->DocDueDate,
            'DiscPrcnt' => $this->DiscPrcnt,
            'DiscSum' => $this->DiscSum,
            'DocRate' => $this->DocRate,
            'DocTotal' => $this->DocTotal,
            'PaidToDate' => $this->PaidToDate,
            'GroupNum' => $this->GroupNum,
            'SlpCode' => $this->SlpCode,
            'Series' => $this->Series,
            'TaxDate' => $this->TaxDate,
            'UserSign' => $this->UserSign,
            'UserSign2' => $this->UserSign2,
            'U_4MOTIVOCANCELADO' => $this->U_4MOTIVOCANCELADO,
            'U_4SUBTOTAL' => $this->U_4SUBTOTAL,
            'PriceListNum' => $this->PriceListNum,
            'estadosend' => $this->estadosend,
            'fecharegistro' => $this->fecharegistro,
            'fechaupdate' => $this->fechaupdate,
            'fechasend' => $this->fechasend,
            'id' => $this->id,
            'idUser' => $this->idUser,
            'estado' => $this->estado,
            'gestion' => $this->gestion,
            'mes' => $this->mes,
            'correlativo' => $this->correlativo,
            'DocTotalPay' => $this->DocTotalPay,
            'TotalDiscMonetary' => $this->TotalDiscMonetary,
            'TotalDiscPrcnt' => $this->TotalDiscPrcnt,
            'DocNumSAP' => $this->DocNumSAP,
            'actsl' => $this->actsl,
            'U_LB_TipoFactura' => $this->U_LB_TipoFactura,
            'U_LB_TotalNCND' => $this->U_LB_TotalNCND,
            'Reserve' => $this->Reserve,
        ]);

        $query->andFilterWhere(['like', 'DocEntry', $this->DocEntry])
            ->andFilterWhere(['like', 'DocType', $this->DocType])
            ->andFilterWhere(['like', 'canceled', $this->canceled])
            ->andFilterWhere(['like', 'Printed', $this->Printed])
            ->andFilterWhere(['like', 'DocStatus', $this->DocStatus])
            ->andFilterWhere(['like', 'CardCode', $this->CardCode])
            ->andFilterWhere(['like', 'CardName', $this->CardName])
            ->andFilterWhere(['like', 'NumAtCard', $this->NumAtCard])
            ->andFilterWhere(['like', 'DocCur', $this->DocCur])
            ->andFilterWhere(['like', 'Ref1', $this->Ref1])
            ->andFilterWhere(['like', 'Ref2', $this->Ref2])
            ->andFilterWhere(['like', 'Comments', $this->Comments])
            ->andFilterWhere(['like', 'JrnlMemo', $this->JrnlMemo])
            ->andFilterWhere(['like', 'LicTradNum', $this->LicTradNum])
            ->andFilterWhere(['like', 'Address', $this->Address])
            ->andFilterWhere(['like', 'CreateDate', $this->CreateDate])
            ->andFilterWhere(['like', 'UpdateDate', $this->UpdateDate])
            ->andFilterWhere(['like', 'U_4NIT', $this->U_4NIT])
            ->andFilterWhere(['like', 'U_4RAZON_SOCIAL', $this->U_4RAZON_SOCIAL])
            ->andFilterWhere(['like', 'U_LATITUD', $this->U_LATITUD])
            ->andFilterWhere(['like', 'U_LONGITUD', $this->U_LONGITUD])
            ->andFilterWhere(['like', 'U_4DOCUMENTOORIGEN', $this->U_4DOCUMENTOORIGEN])
            ->andFilterWhere(['like', 'U_4MIGRADOCONCEPTO', $this->U_4MIGRADOCONCEPTO])
            ->andFilterWhere(['like', 'U_4MIGRADO', $this->U_4MIGRADO])
            ->andFilterWhere(['like', 'idDocPedido', $this->idDocPedido])
            ->andFilterWhere(['like', 'rowNum', $this->rowNum])
            ->andFilterWhere(['like', 'PayTermsGrpCode', $this->PayTermsGrpCode])
            ->andFilterWhere(['like', 'UNumFactura', $this->UNumFactura])
            ->andFilterWhere(['like', 'ControlCode', $this->ControlCode])
            ->andFilterWhere(['like', 'Indicator', $this->Indicator])
            ->andFilterWhere(['like', 'ShipToCode', $this->ShipToCode])
            ->andFilterWhere(['like', 'ControlAccount', $this->ControlAccount])
            ->andFilterWhere(['like', 'U_LB_NumeroFactura', $this->U_LB_NumeroFactura])
            ->andFilterWhere(['like', 'U_LB_EstadoFactura', $this->U_LB_EstadoFactura])
            ->andFilterWhere(['like', 'U_LB_NumeroAutorizac', $this->U_LB_NumeroAutorizac])
            ->andFilterWhere(['like', 'clone', $this->clone])
            ->andFilterWhere(['like', 'giftcard', $this->giftcard]);

        return $dataProvider;
    }
}
