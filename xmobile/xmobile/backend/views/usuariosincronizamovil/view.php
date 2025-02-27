<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="usuariosincronizamovil-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fecha',
            'idUsuario',
            'idSucursal',
            'equipo',
            'servicio',
        ],
    ]) ?>

</div>
