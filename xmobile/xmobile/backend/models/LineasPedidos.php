<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "lineas_pedidos".
 *
 * @property int $id
 * @property string $DocEntry
 * @property int $DocNum
 * @property int $LineNum
 * @property int $BaseType
 * @property int $BaseEntry
 * @property int $BaseLine
 * @property string $LineStatus
 * @property string $ItemCode
 * @property string $Dscription
 * @property int $Quantity
 * @property int $OpenQty
 * @property int $Price
 * @property string $Currency
 * @property int $DiscPrcnt
 * @property int $LineTotal
 * @property string $WhsCode
 * @property string $CodeBars
 * @property int $PriceAfVAT
 * @property string $TaxCode
 * @property int $U_4DESCUENTO
 * @property string $U_4LOTE
 * @property int $GrossBase
 * @property int $idDocumento
 * @property string $fechaAdd
 */
class LineasPedidos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lineas_pedidos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DocNum', 'LineNum', 'BaseType', 'BaseEntry', 'BaseLine', 'Quantity', 'OpenQty', 'Price', 'DiscPrcnt', 'LineTotal', 'PriceAfVAT', 'U_4DESCUENTO', 'GrossBase', 'idDocumento'], 'integer'],
            [['fechaAdd'], 'required'],
            [['fechaAdd'], 'safe'],
            [['DocEntry', 'LineStatus', 'ItemCode', 'Dscription', 'Currency', 'WhsCode', 'CodeBars', 'TaxCode', 'U_4LOTE'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'DocEntry' => 'Doc Entry',
            'DocNum' => 'Doc Num',
            'LineNum' => 'Line Num',
            'BaseType' => 'Base Type',
            'BaseEntry' => 'Base Entry',
            'BaseLine' => 'Base Line',
            'LineStatus' => 'Line Status',
            'ItemCode' => 'Item Code',
            'Dscription' => 'Dscription',
            'Quantity' => 'Quantity',
            'OpenQty' => 'Open Qty',
            'Price' => 'Price',
            'Currency' => 'Currency',
            'DiscPrcnt' => 'Disc Prcnt',
            'LineTotal' => 'Line Total',
            'WhsCode' => 'Whs Code',
            'CodeBars' => 'Code Bars',
            'PriceAfVAT' => 'Price Af Vat',
            'TaxCode' => 'Tax Code',
            'U_4DESCUENTO' => 'U 4 Descuento',
            'U_4LOTE' => 'U 4 Lote',
            'GrossBase' => 'Gross Base',
            'idDocumento' => 'Id Documento',
            'fechaAdd' => 'Fecha Add',
        ];
    }
}
