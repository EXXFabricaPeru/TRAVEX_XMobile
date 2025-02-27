<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="geolocalizacion-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idequipox',
            'latitud',
            'longitud',
            'fecha',
            'hora',
            'idcliente',
            'documentocod',
            'tipodoc',
            'estado',
            'actividad',
            'anexo',
            'usuario',
            'status',
            'dateUpdate',
        ],
    ]) ?>

</div>
