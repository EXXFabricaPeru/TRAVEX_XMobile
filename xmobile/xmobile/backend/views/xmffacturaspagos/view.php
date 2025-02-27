<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="xmffacturaspagos-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idCabecera',
            'clienteId',
            'nro_recibo',
            'documentoId',
            'docentry',
            'monto',
            'CardName',
            'saldo',
            'nroFactura',
            'DocTotal',
            'cuota',
        ],
    ]) ?>

</div>
