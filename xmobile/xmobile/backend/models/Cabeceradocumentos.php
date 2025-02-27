<?php

namespace backend\models;

use Carbon\Carbon;
use Yii;

/**
 * This is the model class for table "cabeceradocumentos".
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
 * @property int $idUser
 * @property int $estado
 * @property int $gestion
 * @property int $mes
 * @property int $correlativo
 * @property string $rowNum
 * @property double $TotalDiscMonetary
 * @property double $TotalDicPrcnt
 * @property string $UNumFactura
 * @property string ControlCode
 * @property string sucursalxId
 * @property string equipoId
 * @property string Comments
 * @property string xMOB_Venta1
 * @property string xMOB_Venta2
 * @property string xMOB_Venta3
 * @property string xMOB_Venta4
 * @property 
 *
 * @property Clientes $cardCode
 * @property User $user
 * @property Detalledocumentos[] $detalledocumentos
 * @property string xMOB_ConceptoAn
 * @property string campania
 * @property int monto
 * @property int eliminado
 */
class Cabeceradocumentos extends \yii\db\ActiveRecord {
    /**
     * @var Servislayer
     */

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cabeceradocumentos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['DocType', 'DocDate', 'DocDueDate', 'CardName', 'TaxDate', 'Address', 'fecharegistro', 'fechasend', 'idDocPedido'], 'required'],
            [['DocNum', 'DiscPrcnt', 'DiscSum', 'DocRate', 'DocTotal', 'PaidToDate', 'GroupNum', 'SlpCode', 'Series', 'UserSign', 'UserSign2', 'U_4MOTIVOCANCELADO', 'U_4SUBTOTAL', 'PriceListNum', 'estadosend', 'idUser', 'estado', 'gestion', 'mes', 'correlativo', 'U_LB_TipoFactura', 'U_LB_TotalNCND', 'eliminado'], 'integer'],
            [['DocDate', 'DocDueDate', 'TaxDate', 'fecharegistro', 'fechaupdate', 'fechasend', 'clone'], 'safe'],
            [['DocEntry', 'canceled', 'Printed', 'DocStatus', 'CardCode', 'CardName', 'NumAtCard', 'DocCur', 'Ref1', 'Ref2', 'Comments', 'JrnlMemo', 'LicTradNum', 'Address', 'CreateDate', 'UpdateDate', 'U_4NIT', 'U_4RAZON_SOCIAL', 'U_LATITUD', 'U_LONGITUD', 'U_4DOCUMENTOORIGEN', 'U_4MIGRADOCONCEPTO', 'U_4MIGRADO', 'idDocPedido', 'rowNum', 'UNumFactura', 'ControlCode', 'sucursalxId', 'equipoId', 'Indicator', 'ShipToCode', 'ControlAccount', 'U_LB_NumeroFactura', 'U_LB_EstadoFactura', 'U_LB_NumeroAutorizac', 'giftcard','xMOB_Venta1','xMOB_Venta2','xMOB_Venta3','xMOB_Venta4','xMOB_Venta5','xMOB_ConceptoAn','U_EXX_CODTRANS','U_EXX_NOMTRANS','U_EXX_RUCTRANS','U_EXX_DIRTRANS','U_EXX_NOMCONDU','U_EXX_LICCONDU','U_EXX_PLACAVEH','U_EXX_MARCAVEH','U_EXX_PLACATOL'], 'string', 'max' => 255],
            [['DocType'], 'string', 'max' => 10],
            [['CardCode'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['CardCode' => 'CardCode']],
            [['idUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['idUser' => 'id']],
            [['monto'], 'double'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'DocEntry' => 'DocEntry',
            'DocNum' => 'Doc Num',
            'DocType' => 'Tipo de doc',
            'canceled' => 'Canceled',
            'Printed' => 'Printed',
            'DocStatus' => 'Doc Status',
            'DocDate' => 'Doc Date',
            'DocDueDate' => 'Doc Due Date',
            'CardCode' => 'Codigo de cliente',
            'CardName' => 'Nombre del cliente',
            'NumAtCard' => 'Num At Card',
            'DiscPrcnt' => 'Disc Prcnt',
            'DiscSum' => 'Disc Sum',
            'DocCur' => 'Moneda',
            'DocRate' => 'Doc Rate',
            'DocTotal' => 'Total',
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
            'idDocPedido' => 'Codigo',
            'idUser' => 'Usuario',
            'estado' => 'Estado',
            'gestion' => 'Gestion',
            'mes' => 'Mes',
            'correlativo' => 'Correlativo',
            'rowNum' => 'Row Num',
            'clone' => 'Origen clonacion',
            'giftcard' => 'Giftcard',
            'Indicator' => 'Indicator',
            'Reserve' => 'Tipo fac',
            'ShipToCode' => 'Ship To Code',
            'ControlAccount' => 'Control Account',
            'U_LB_NumeroFactura' => '* Numero Factura',
            'U_LB_EstadoFactura' => 'U LB Estado Factura',
            'U_LB_NumeroAutorizac' => 'U LB Numero Autorizac',
            'U_LB_TipoFactura' => 'U LB Tipo Factura',
            'U_LB_TotalNCND' => 'U LB Total NCND',
            'xMOB_Venta1' => 'xMOB_Venta1',
            'xMOB_Venta2' => 'xMOB_Venta2',
            'xMOB_Venta3' => 'xMOB_Venta3',
            'xMOB_Venta2' => 'xMOB_Venta4',
            'xMOB_Venta3' => 'xMOB_Venta5',
            'xMOB_ConceptoAn'=>"Concepto anulaci�n",
            'campania' => 'campania',
            'monto'=>"monto",
            'eliminado'=>"eliminado"
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCardCode() {
        return $this->hasOne(Clientes::className(), ['CardCode' => 'CardCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'idUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalledocumentos() {
        return $this->hasMany(Detalledocumentos::className(), ['idCabecera' => 'id'])->orderBy('LineNum asc');//->orderBy(['BaseLine' => SORT_ASC]);
    }

    public static function getDescuentos($cliente, $producto, $listaPrecio, $cantidad) {
        $descuentos = Yii::$app->db->createCommand("CALL pa_grupo_descuento_especifico(:cliente,:producto)")
                ->bindParam(':cliente', $cliente)
                ->bindParam(':producto', $producto)
                ->queryOne();
        if (isset($descuentos["Result"])) {
            $descuentos = Yii::$app->db->createCommand("CALL pa_grupo_descuento_proveedor(:cliente,:producto)")
                    ->bindParam(':cliente', $cliente)
                    ->bindParam(':producto', $producto)
                    ->queryOne();
        }
        if (isset($descuentos["Result"])) {
            $descuentos = Yii::$app->db->createCommand("CALL pa_grupo_descuento_clientes(:cliente,:producto)")
                    ->bindParam(':cliente', $cliente)
                    ->bindParam(':producto', $producto)
                    ->queryOne();
        }
        if (isset($descuentos["Result"])) {
            $descuentos = Yii::$app->db->createCommand("CALL pa_grupo_descuento_todosclientes(:cliente,:producto)")
                    ->bindParam(':cliente', $cliente)
                    ->bindParam(':producto', $producto)
                    ->queryOne();
        }
        if (isset($descuentos["Result"])) {
            $descuentos = Yii::$app->db->createCommand("CALL pa_descuento_periodo_cantidad(:listaPrecio,:producto,:cantidad)")
                    ->bindParam(':listaPrecio', $listaPrecio)
                    ->bindParam(':producto', $producto)
                    ->bindParam(':cantidad', $cantidad)
                    ->queryOne();
        }
        return $descuentos;
    }

    public static function cerrarPedido($DocEntry) {
        $serviceLayer = new Servislayer();
        $serviceLayer->actiondir = "Orders({$DocEntry})/Close";
        $respuesta = $serviceLayer->executePost([]);
        $pedidos = Cabeceradocumentos::find()
                ->where("DocEntry = {$DocEntry}")
                ->one();
        if ($pedidos) {
            if ($respuesta) {
                $pedidos->estado = 8;
                $pedidos->fechaupdate = Carbon::today();
                $pedidos->update(false);
                return true;
            } else {
                Yii::error("ID-MID:{$pedidos->id};DATA-" . json_encode($respuesta));
            }
        }
        return false;
    }

}