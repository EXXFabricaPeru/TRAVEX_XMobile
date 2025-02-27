<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="contactos-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cardCode',
            'nombre',
            'direccion',
            'telefono1',
            'telefono2',
            'celular',
            'tipo',
            'comentarios',
            'User',
            'Status',
            'DateUpdate',
            'correo',
            'titulo',
        ],
    ]) ?>

</div>
