<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "config_usuarios".
 *
 * @property string $id
 * @property integer $idEstado
 * @property integer $idTipoPrecio
 * @property integer $idTipoImpresora
 * @property integer $estadoListaPrecio
 * @property string $ruta
 * @property string $ctaEfectivo
 * @property string $ctaCheque
 * @property string $ctaTransferencia
 * @property string $ctaFcEfectivo
 * @property string $ctaFcCheque
 * @property string $ctaFcTransferencia
 * @property string $sreOfertaVenta
 * @property string $sreOrdenVenta
 * @property string $sreFactura
 * @property string $sreFacturaReserva
 * @property string $sreCobro
 * @property double $flujoCaja
 * @property string $modInfTributaria
 * @property string $codEmpleadoVenta
 * @property string $codVendedor
 * @property string $nombre
 *
 * @property TiposEstados $estado
 * @property TiposPrecios $tipoPrecio
 * @property TiposImpresoras $tipoImpresora
 */
class ConfigUsuarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codEmpleadoVenta', 'modInfTributaria', 'idEstado', 'idTipoPrecio', 'idTipoImpresora','idUser'], 'integer'],
            [['idEstado', 'codEmpleadoVenta', 'modInfTributaria', 'idTipoPrecio', 'ruta', 'idTipoImpresora','idUser'], 'required'],
            [['flujoCaja'], 'number'],
			[['almacenes','modMoneda'], 'safe'],
            [['ruta', 'ctaEfectivo', 'ctaCheque', 'ctaTransferencia', 'ctaFcEfectivo', 'ctaFcCheque', 'ctaFcTransferencia', 'sreOfertaVenta', 'sreOrdenVenta', 'sreFactura', 'sreFacturaReserva', 'sreCobro', 'modInfTributaria', 'codEmpleadoVenta', 'codVendedor', 'nombre'], 'string', 'max' => 240],
            [['idEstado'], 'exist', 'skipOnError' => true, 'targetClass' => TiposEstados::className(), 'targetAttribute' => ['idEstado' => 'id']],
            [['idTipoPrecio'], 'exist', 'skipOnError' => true, 'targetClass' => TiposPrecios::className(), 'targetAttribute' => ['idTipoPrecio' => 'id']],
            [['idTipoImpresora'], 'exist', 'skipOnError' => true, 'targetClass' => TiposImpresoras::className(), 'targetAttribute' => ['idTipoImpresora' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idEstado' => 'Id Estado',
            'idTipoPrecio' => 'Id Tipo Precio',
            'idTipoImpresora' => 'Id Tipo Impresora',
            'ruta' => 'Ruta',
            'ctaEfectivo' => 'Cta Efectivo',
            'ctaCheque' => 'Cta Cheque',
            'ctaTransferencia' => 'Cta Transferencia',
            'ctaFcEfectivo' => 'Cta Fc Efectivo',
            'ctaFcCheque' => 'Cta Fc Cheque',
            'ctaFcTransferencia' => 'Cta Fc Transferencia',
            'sreOfertaVenta' => 'Sre Oferta Venta',
            'sreOrdenVenta' => 'Sre Orden Venta',
            'sreFactura' => 'Sre Factura',
            'sreFacturaReserva' => 'Sre Factura Reserva',
            'sreCobro' => 'Sre Cobro',
            'flujoCaja' => 'Flujo Caja',
            'modInfTributaria' => 'Mod Inf Tributaria',
            'codEmpleadoVenta' => 'Cod Empleado Venta',
            'codVendedor' => 'Cod Vendedor',
            'nombre' => 'Nombre',
            'almacenes' => 'Almacenes',
            'idUser' => 'Usuario'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(TiposEstados::className(), ['id' => 'idEstado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoPrecio()
    {
        return $this->hasOne(TiposPrecios::className(), ['id' => 'idTipoPrecio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoImpresora()
    {
        return $this->hasOne(TiposImpresoras::className(), ['id' => 'idTipoImpresora']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpdocumentos()
    {
        return $this->hasMany(OpcionesDocumento::className(), ['configuracionId' => 'id']);
    }
}
