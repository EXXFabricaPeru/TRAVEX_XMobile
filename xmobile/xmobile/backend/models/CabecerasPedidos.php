<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cabeceras_pedidos".
 *
 * @property string $DocEntry
 * @property int $DocNum
 * @property string $DocType
 * @property string $canceled
 * @property string $Printed
 * @property string $DocStatus
 * @property string $DocDate
 * @property string $DocDueDate
 * @property string $CardCode
 * @property string $CardName
 * @property string $NumAtCard
 * @property int $DiscPrcnt
 * @property int $DiscSum
 * @property string $DocCur
 * @property int $DocRate
 * @property int $DocTotal
 * @property int $PaidToDate
 * @property string $Ref1
 * @property string $Ref2
 * @property string $Comments
 * @property string $JrnlMemo
 * @property int $GroupNum
 * @property int $SlpCode
 * @property int $Series
 * @property string $TaxDate
 * @property string $LicTradNum
 * @property string $Address
 * @property int $UserSign
 * @property string $CreateDate
 * @property int $UserSign2
 * @property string $UpdateDate
 * @property int $U_4MOTIVOCANCELADO
 * @property string $U_4NIT
 * @property string $U_4RAZON_SOCIAL
 * @property string $U_LATITUD
 * @property string $U_LONGITUD
 * @property int $U_4SUBTOTAL
 * @property string $U_4DOCUMENTOORIGEN
 * @property string $U_4MIGRADOCONCEPTO
 * @property string $U_4MIGRADO
 * @property int $PriceListNum
 * @property int $estadosend
 * @property string $fecharegistro
 * @property string $fechaupdate
 * @property string $fechasend
 * @property int $id
 * @property string $idDocPedido
 * @property int $gestion
 * @property int $mes
 * @property int $idUser
 * @property int $correlativo
 * @property int $estado
 */
class CabecerasPedidos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cabeceras_pedidos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*[['DocNum', 'DocType', 'DocDate', 'DocDueDate', 'CardCode', 'CardName', 'TaxDate', 'Address', 'fecharegistro', 'fechasend'], 'required'],
            [['DocNum', 'DiscPrcnt', 'DiscSum', 'DocRate', 'DocTotal', 'PaidToDate', 'GroupNum', 'SlpCode', 'Series', 'UserSign', 'UserSign2', 'U_4MOTIVOCANCELADO', 'U_4SUBTOTAL', 'PriceListNum', 'estadosend'], 'integer'],
            [['DocDate', 'DocDueDate', 'TaxDate', 'fecharegistro', 'fechaupdate', 'fechasend'], 'safe'],
            [['DocEntry', 'canceled', 'Printed', 'DocStatus', 'CardCode', 'CardName', 'NumAtCard', 'DocCur', 'Ref1', 'Ref2', 'Comments', 'JrnlMemo', 'LicTradNum', 'Address', 'CreateDate', 'UpdateDate', 'U_4NIT', 'U_4RAZON_SOCIAL', 'U_LATITUD', 'U_LONGITUD', 'U_4DOCUMENTOORIGEN', 'U_4MIGRADOCONCEPTO', 'U_4MIGRADO'], 'string', 'max' => 255],
            [['DocType'], 'string', 'max' => 10],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'DocEntry' => 'Doc Entry',
            'DocNum' => 'Doc Num',
            'DocType' => 'Doc Type',
            'canceled' => 'Canceled',
            'Printed' => 'Printed',
            'DocStatus' => 'Doc Status',
            'DocDate' => 'Doc Date',
            'DocDueDate' => 'Doc Due Date',
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'NumAtCard' => 'Num At Card',
            'DiscPrcnt' => 'Disc Prcnt',
            'DiscSum' => 'Disc Sum',
            'DocCur' => 'Doc Cur',
            'DocRate' => 'Doc Rate',
            'DocTotal' => 'Doc Total',
            'PaidToDate' => 'Paid To Date',
            'Ref1' => 'Ref1',
            'Ref2' => 'Ref2',
            'Comments' => 'Comments',
            'JrnlMemo' => 'Jrnl Memo',
            'GroupNum' => 'Group Num',
            'SlpCode' => 'Slp Code',
            'Series' => 'Series',
            'TaxDate' => 'Tax Date',
            'LicTradNum' => 'Lic Trad Num',
            'Address' => 'Address',
            'UserSign' => 'User Sign',
            'CreateDate' => 'Create Date',
            'UserSign2' => 'User Sign2',
            'UpdateDate' => 'Update Date',
            'U_4MOTIVOCANCELADO' => 'U 4 Motivocancelado',
            'U_4NIT' => 'U 4 Nit',
            'U_4RAZON_SOCIAL' => 'U 4 Razon Social',
            'U_LATITUD' => 'U Latitud',
            'U_LONGITUD' => 'U Longitud',
            'U_4SUBTOTAL' => 'U 4 Subtotal',
            'U_4DOCUMENTOORIGEN' => 'U 4 Documentoorigen',
            'U_4MIGRADOCONCEPTO' => 'U 4 Migradoconcepto',
            'U_4MIGRADO' => 'U 4 Migrado',
            'PriceListNum' => 'Price List Num',
            'estadosend' => 'Estadosend',
            'fecharegistro' => 'Fecharegistro',
            'fechaupdate' => 'Fechaupdate',
            'fechasend' => 'Fechasend',
            'id' => 'ID',
        ];
    }
}
