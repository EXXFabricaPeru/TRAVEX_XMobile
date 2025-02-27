<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="empresa-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'direccion',
            'telefono1',
            'telefono2',
            'nit',
            'pais',
            'ciudad',
            'actividad',
            'usuario',
            'status',
            'dateUpdate',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
