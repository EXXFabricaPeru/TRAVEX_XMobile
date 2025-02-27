<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="anulaciondocmovil-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fechaRegistro',
            'usuario',
            'docDate',
            'docType',
            'docEntry',
            'motivoAnulacion',
            'estado',
            'idUser',
        ],
    ]) ?>

</div>
