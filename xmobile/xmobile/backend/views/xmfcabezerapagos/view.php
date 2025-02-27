<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="xmfcabezerapagos-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nro_recibo',
            'correlativo',
            'usuario',
            'documentoId',
            'fecha',
            'hora',
            'monto_total',
            'tipo',
            'otpp',
            'tipo_cambio',
            'moneda',
            'cliente_carcode',
            'razon_social',
            'nit',
            'estado',
            'cancelado',
            'tipoTarjeta',
            'equipo',
            'fechaSistema',
            'TransId',
            'latitud',
            'longitud',
            'idDocumento',
        ],
    ]) ?>

</div>
