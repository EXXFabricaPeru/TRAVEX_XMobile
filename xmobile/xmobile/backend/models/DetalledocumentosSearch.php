<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Detalledocumentos;

/**
 * DetalledocumentosSearch represents the model behind the search form of `backend\models\Detalledocumentos`.
 */
class DetalledocumentosSearch extends Detalledocumentos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'DocNum', 'LineNum', 'BaseType', 'BaseEntry', 'BaseLine', 'Quantity', 'OpenQty', 'PriceAfVAT', 'GrossBase', 'idCabecera', 'idProductoPrecio', 'actsl', 'BaseDocEntry', 'BaseDocLine', 'BaseDocType', 'BaseDocumentReference', 'GrossPrice', 'TargetAbsEntry'], 'integer'],
            [['DocEntry', 'LineStatus', 'ItemCode', 'Dscription', 'Currency', 'WhsCode', 'CodeBars', 'TaxCode', 'U_4LOTE', 'idDocumento', 'fechaAdd', 'unidadid', 'SalesUnitLength', 'SalesUnitWidth', 'SalesUnitHeight', 'SalesUnitVolume', 'ICET', 'TreeType', 'WarehouseCode', 'CorrectionInvoiceItem', 'Status', 'Stock'], 'safe'],
            [['Price', 'DiscPrcnt', 'LineTotal', 'U_4DESCUENTO', 'tc', 'DiscMonetary', 'LineTotalPay', 'DiscTotalPrcnt', 'DiscTotalMonetary', 'ICEE', 'ICEP'], 'number'],
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
        $query = Detalledocumentos::find();

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
            'DocNum' => $this->DocNum,
            'LineNum' => $this->LineNum,
            'BaseType' => $this->BaseType,
            'BaseEntry' => $this->BaseEntry,
            'BaseLine' => $this->BaseLine,
            'Quantity' => $this->Quantity,
            'OpenQty' => $this->OpenQty,
            'Price' => $this->Price,
            'DiscPrcnt' => $this->DiscPrcnt,
            'LineTotal' => $this->LineTotal,
            'PriceAfVAT' => $this->PriceAfVAT,
            'U_4DESCUENTO' => $this->U_4DESCUENTO,
            'GrossBase' => $this->GrossBase,
            'fechaAdd' => $this->fechaAdd,
            'tc' => $this->tc,
            'idCabecera' => $this->idCabecera,
            'idProductoPrecio' => $this->idProductoPrecio,
            'DiscMonetary' => $this->DiscMonetary,
            'LineTotalPay' => $this->LineTotalPay,
            'DiscTotalPrcnt' => $this->DiscTotalPrcnt,
            'DiscTotalMonetary' => $this->DiscTotalMonetary,
            'ICEE' => $this->ICEE,
            'ICEP' => $this->ICEP,
            'actsl' => $this->actsl,
            'BaseDocEntry' => $this->BaseDocEntry,
            'BaseDocLine' => $this->BaseDocLine,
            'BaseDocType' => $this->BaseDocType,
            'BaseDocumentReference' => $this->BaseDocumentReference,
            'GrossPrice' => $this->GrossPrice,
            'TargetAbsEntry' => $this->TargetAbsEntry,
        ]);

        $query->andFilterWhere(['like', 'DocEntry', $this->DocEntry])
            ->andFilterWhere(['like', 'LineStatus', $this->LineStatus])
            ->andFilterWhere(['like', 'ItemCode', $this->ItemCode])
            ->andFilterWhere(['like', 'Dscription', $this->Dscription])
            ->andFilterWhere(['like', 'Currency', $this->Currency])
            ->andFilterWhere(['like', 'WhsCode', $this->WhsCode])
            ->andFilterWhere(['like', 'CodeBars', $this->CodeBars])
            ->andFilterWhere(['like', 'TaxCode', $this->TaxCode])
            ->andFilterWhere(['like', 'U_4LOTE', $this->U_4LOTE])
            ->andFilterWhere(['like', 'idDocumento', $this->idDocumento])
            ->andFilterWhere(['like', 'unidadid', $this->unidadid])
            ->andFilterWhere(['like', 'SalesUnitLength', $this->SalesUnitLength])
            ->andFilterWhere(['like', 'SalesUnitWidth', $this->SalesUnitWidth])
            ->andFilterWhere(['like', 'SalesUnitHeight', $this->SalesUnitHeight])
            ->andFilterWhere(['like', 'SalesUnitVolume', $this->SalesUnitVolume])
            ->andFilterWhere(['like', 'ICET', $this->ICET])
            ->andFilterWhere(['like', 'TreeType', $this->TreeType])
            ->andFilterWhere(['like', 'WarehouseCode', $this->WarehouseCode])
            ->andFilterWhere(['like', 'CorrectionInvoiceItem', $this->CorrectionInvoiceItem])
            ->andFilterWhere(['like', 'Status', $this->Status])
            ->andFilterWhere(['like', 'Stock', $this->Stock]);

        return $dataProvider;
    }
}
