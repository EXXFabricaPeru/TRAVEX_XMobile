<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="equipoxcuentascontables-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'equipoxId',
            'cuentaEfectivo',
            'cuentaCheque',
            'cuentaTranferencia',
            'cuentaTarjeta',
            'cuentaAnticipos',
            'cuentaEfectivoUSD',
            'cuentaChequeUSD',
            'cuentaTranferenciaUSD',
            'cuentaTarjetaUSD',
            'cuentaAnticiposUSD',
        ],
    ])
    ?>

</div>
