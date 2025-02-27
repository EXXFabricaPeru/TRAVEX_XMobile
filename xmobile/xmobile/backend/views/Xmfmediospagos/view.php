<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="xmfmediospagos-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idCabecera',
            'nro_recibo',
            'documentoId',
            'formaPago',
            'monto',
            'numCheque',
            'numComprobante',
            'numTarjeta',
            'bancoCode',
            'fecha',
            'cambio',
            'monedaDolar',
            'monedaLocal',
            'centro',
            'baucher',
            'checkdate',
            'transferencedate',
            'CreditCard',
        ],
    ]) ?>

</div>
