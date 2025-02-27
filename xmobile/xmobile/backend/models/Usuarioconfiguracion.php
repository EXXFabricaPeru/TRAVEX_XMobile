<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuarioconfiguracion".
 *
 * @property int $id
 * @property int|null $idEstado
 * @property int|null $idTipoPrecio
 * @property int|null $estadoListaPrecio
 * @property int|null $idTipoImpresora
 * @property string|null $ruta
 * @property string|null $ctaEfectivo
 * @property string|null $ctaCheque
 * @property string|null $ctaTransferencia
 * @property string|null $ctaFcEfectivo
 * @property string|null $ctaFcCheque
 * @property string|null $ctaFcTransferencia
 * @property string|null $sreOfertaVenta
 * @property string|null $sreOrdenVenta
 * @property string|null $sreFactura
 * @property string|null $sreFacturaReserva
 * @property string|null $sreCobro
 * @property float|null $flujoCaja
 * @property string|null $modInfTributaria
 * @property int|null $codEmpleadoVenta
 * @property string|null $codVendedor
 * @property string|null $nombre
 * @property string $almacenes
 * @property int $idUser
 * @property int|null $modMoneda
 * @property int|null $estadoAlmacenes
 * @property string|null $ctaTarjeta
 * @property string|null $ctaFcTarjeta
 * @property int|null $crearCliente
 * @property string|null $moneda
 * @property int|null $territorio
 * @property int|null $grupoCliente
 * @property int|null $listaPrecios
 * @property int|null $descuentos
 * @property int|null $totalDescuentoDocumento
 * @property int|null $editarDocumento
 * @property int|null $aperturaCaja
 * @property int|null $cierreCaja
 * @property string|null $totalDescuento
 * @property string|null $condicionPago
 * @property string|null $ctaanticipo
 * @property string|null $multiListaPrecios
 * @property int|null anularfacturas
 * @property int|null anularentregas
 * @property int|null anularcobros
 * @property  int|null grupoClienteDosificacion
 * @property int|null TipoUsuario
 * @property string|null $multiCamposUsuarios
 *
 * @property Opcionesdocumento[] $opcionesdocumentos
 * @property User $idUser0
 */
class Usuarioconfiguracion extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarioconfiguracion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idEstado', 'idTipoPrecio', 'estadoListaPrecio', 'idTipoImpresora', 'codEmpleadoVenta', 'idUser', 'modMoneda', 'estadoAlmacenes', 'crearCliente', 'territorio', 'grupoCliente', 'listaPrecios', 'descuentos', 'totalDescuentoDocumento', 'editarDocumento', 'aperturaCaja', 'cierreCaja', 'anularfacturas', 'anularentregas', 'anularcobros','grupoClienteDosificacion','TipoUsuario','seriesCliente','seriesOferta','seriesPedido','seriesPago','seriesEntrega','zonaFranca'], 'integer'],
            [['flujoCaja'], 'number'],
            [[
                'almacenes', 'idUser', 'listaPrecios', 'grupoCliente', 'territorio', 'moneda', 'totalDescuentoDocumento', 'multiListaPrecios', 'totalDescuento',
                'permisoOferta', 'permisoFactura', 'permisoEntrega', 'permisoPedido', 'permisoPagosAnticipados', 'permisoPagosFacturasLocales', 'permisoPagoFacturasImportadas', 'permisoImportarDocumentosOferta', 'permisoImportarDocumentosPedido',
                'permisoImportarDocumentosFactura', 'permisoImportarDocumentosEntregas', 'descuentosLinea', 'descuentosDocumento', 'validarStock', 'controlarModificarListaPrecios', 'controlarCambioMoneda', 'permisoAnularPedido', 'permisoAnularOferta',
                'permisoCopiarPedido', 'permisoCopiarOferta', 'permisoCrearClientes', 'permisoEditarClientes'
            ], 'required'],
            [['ruta', 'ctaEfectivo', 'ctaCheque', 'ctaTransferencia', 'ctaFcEfectivo', 'ctaFcCheque', 'ctaFcTransferencia', 'sreOfertaVenta', 'sreOrdenVenta', 'sreFactura', 'sreFacturaReserva', 'sreCobro', 'modInfTributaria', 'codVendedor', 'nombre', 'ctaTarjeta', 'ctaFcTarjeta'], 'string', 'max' => 240],
            [['almacenes', 'totalDescuento', 'ctaanticipo', 'multiListaPrecios','multiCamposUsuarios'], 'string', 'max' => 255],
            [['moneda'], 'string', 'max' => 5],
            [['condicionPago'], 'string', 'max' => 10],
            [['modlitaprecios', 'accessstock', 'multiListaPrecios','multiCamposUsuarios'], 'safe'],
            [['idUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['idUser' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idEstado' => 'Configuracion activa?',
            'idTipoPrecio' => 'Modificar precio?',
            'estadoListaPrecio' => 'Lista precio',
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
            'modInfTributaria' => 'Modificar Inf Tributaria?',
            'codEmpleadoVenta' => 'Vendedor asignado (SAP)',
            'codVendedor' => 'Cod Vendedor',
            'nombre' => 'Nombre',
            'almacenes' => 'Almacenes',
            'idUser' => 'Id User',
            'modMoneda' => 'Cambio de moneda',
            'estadoAlmacenes' => 'Estado Almacenes',
            'ctaTarjeta' => 'Cta Tarjeta',
            'ctaFcTarjeta' => 'Cta Fc Tarjeta',
            'crearCliente' => 'Crear Cliente',
            'moneda' => 'Moneda',
            'territorio' => 'Territorio',
            'grupoCliente' => 'Grupo del cliente',
            'listaPrecios' => 'Lista Precios',
            'descuentos' => 'Descuentos',
            'totalDescuentoDocumento' => 'Max descuento por documento',
            'editarDocumento' => 'Editar Documento',
            'aperturaCaja' => 'Apertura Caja',
            'cierreCaja' => 'Cierre Caja',
            'totalDescuento' => 'Descuentopor linea',
            'condicionPago' => 'Condicion Pago',
            'ctaanticipo' => 'Ctaanticipo',
            'multiListaPrecios' => 'Multi Lista Precios',
            'modlitaprecios' => 'Tienes acceso a modificar lista de precios',
            'accessstock' => 'Validar STOCK Móvil',
            'anularfacturas' => 'Puede anular facturas',
            'anularentregas' => 'Puede anular entregas',
            'anularcobros' => 'Puede anular cobros',
            'permisoOferta' => 'Crear documento de Oferta',
            'permisoFactura' => 'Crear documento de Factura',
            'permisoEntrega' => 'Crear documento de Entrega',
            'permisoPedido' => 'Crear documento de Pedido',
            'permisoAnularPedido' => 'Anular documento de Pedido',
            'permisoAnularOferta' => 'Anular documento de Oferta',
            'permisoCopiarPedido' => 'Copiar documento de Pedido',
            'permisoCopiarOferta' => 'Copiar documento de Oferta',
            'permisoPagosAnticipados' => 'Pagos anticipados',
            'permisoPagosFacturasLocales' => 'Pagos a facturas locales',
            'permisoPagoFacturasImportadas' => 'Pago a facturas importadas',
            'descuentosLinea' => 'Descuentos por lineas',
            'descuentosDocumento' => 'Descuentos de documentos',
            'validarStock' => 'Validar STOCK POS',
            'controlarModificarListaPrecios' => 'Modificar lista de precios',
            'controlarCambioMoneda' => 'Cambio de moneda',
            'permisoCrearClientes' => 'Crear clientes',
            'permisoEditarClientes' => 'Editar clientes',
            'permisoImportarDocumentosOferta' => 'Importar documentos de Ofertas',
            'permisoImportarDocumentosPedido' => 'Importar documentos de Pedidos',
            'permisoImportarDocumentosFactura' => 'Importar documentos de Facturas',
            'permisoImportarDocumentosEntregas' => 'Importar documentos de Entregas',
            'grupoClienteDosificacion' => 'Grupo de dosificación',
            'TipoUsuario' => 'Despachador',
            'multiCamposUsuarios'=>'Multi Campos Usuario'
        ];
    }

    /**
     * Gets query for [[Opcionesdocumentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpcionesdocumentos()
    {
        return $this->hasMany(Opcionesdocumento::className(), ['configuracionId' => 'id']);
    }

    /**
     * Gets query for [[IdUser0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'idUser']);
    }

}
