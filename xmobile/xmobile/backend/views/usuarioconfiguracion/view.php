<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="usuarioconfiguracion-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idEstado',
            'idTipoPrecio',
            'estadoListaPrecio',
            'idTipoImpresora',
            'ruta',
            'ctaEfectivo',
            'ctaCheque',
            'ctaTransferencia',
            'ctaFcEfectivo',
            'ctaFcCheque',
            'ctaFcTransferencia',
            'sreOfertaVenta',
            'sreOrdenVenta',
            'sreFactura',
            'sreFacturaReserva',
            'sreCobro',
            'flujoCaja',
            'modInfTributaria',
            'codEmpleadoVenta',
            'codVendedor',
            'nombre',
            'almacenes',
            'idUser',
            'modMoneda',
            'estadoAlmacenes',
            'ctaTarjeta',
            'ctaFcTarjeta',
            'crearCliente',
            'moneda',
            'territorio',
            'grupoCliente',
            'listaPrecios',
            'descuentos',
            'totalDescuentoDocumento',
            'editarDocumento',
            'aperturaCaja',
            'cierreCaja',
            'totalDescuento',
            'condicionPago',
            'ctaanticipo',
            'multiListaPrecios',
            'multiCamposUsuarios',
        ],
    ]) ?>

</div>
