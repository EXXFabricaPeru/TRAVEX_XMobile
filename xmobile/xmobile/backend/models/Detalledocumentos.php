<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "detalledocumentos".
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
 * @property string $unidadid
 * @property string $tc
 * @property int $idCabecera
 * @property int $idProductoPrecio
 * @property double $DiscTotalPrcnt
 * @property double $DiscTotalMonetary
 * @property string xMOB_Venta1
 * @property string xMOB_Venta2
 * @property string xMOB_Venta3
 * @property string xMOB_Venta4
 * @property string xMOB_Venta5
 * @property string bonificacion
 * @property string codeBonificacionUse
 *
 * @property Cabeceradocumentos $cabecera
 * @property Productos $itemCode
 * @property Productosprecios $productoPrecio
 *  @property int $ListaPrecios
 * 
 */
class Detalledocumentos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detalledocumentos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DocNum', 'LineNum', 'BaseType', 'BaseEntry', 'BaseLine', 'Quantity', 'OpenQty', 'Price', 'DiscPrcnt', 'LineTotal', 'PriceAfVAT', 'U_4DESCUENTO', 'GrossBase', 'idDocumento', 'idCabecera', 'idProductoPrecio','ListaPrecios'], 'integer'],
            [['fechaAdd', 'idCabecera'], 'required'],
            [['fechaAdd'], 'safe'],
            [['tc'], 'number'],
            [['DocEntry', 'LineStatus', 'ItemCode', 'Dscription', 'Currency', 'WhsCode', 'CodeBars', 'TaxCode', 'U_4LOTE','xMOB_Venta1','xMOB_Venta2','xMOB_Venta3','xMOB_Venta4','xMOB_Venta5','bonificacion','codeBonificacionUse'], 'string', 'max' => 255],
            [['unidadid'], 'string', 'max' => 50],
            [['idCabecera'], 'exist', 'skipOnError' => true, 'targetClass' => Cabeceradocumentos::className(), 'targetAttribute' => ['idCabecera' => 'id']],
            [['ItemCode'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['ItemCode' => 'ItemCode']],
            [['idProductoPrecio'], 'exist', 'skipOnError' => true, 'targetClass' => Productosprecios::className(), 'targetAttribute' => ['idProductoPrecio' => 'id']],
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
            'unidadid' => 'Unidadid',
            'tc' => 'Tc',
            'idCabecera' => 'Id Cabecera',
            'idProductoPrecio' => 'Id Producto Precio',
            'xMOB_Venta1' => 'xMOB_Venta1',
            'xMOB_Venta2' => 'xMOB_Venta2',
            'xMOB_Venta3' => 'xMOB_Venta3',
            'xMOB_Venta2' => 'xMOB_Venta4',
            'xMOB_Venta3' => 'xMOB_Venta5',
            'ListaPrecios' => 'Lista Precios',
            'bonificacion' => 'bonificacion',
            'codeBonificacionUse' => 'codeBonificacionUse',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCabecera()
    {
        return $this->hasOne(Cabeceradocumentos::className(), ['id' => 'idCabecera']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemCode()
    {
        return $this->hasOne(Productos::className(), ['ItemCode' => 'ItemCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductoPrecio()
    {
        return $this->hasOne(Productosprecios::className(), ['id' => 'idProductoPrecio']);
    }
}
