<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="rutacabecera-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'idvendedor',
            'fecha',
            'idclienteinicial',
            'longitud',
            'latitud',
            'usuario',
            'status',
            'dateUpdate',
        ],
    ]) ?>

</div>
