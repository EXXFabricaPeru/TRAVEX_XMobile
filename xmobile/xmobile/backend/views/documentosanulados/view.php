<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="viobtienedocumentosanulados-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fechaRegistro',
            'docDate',
            'docEntry',
            'docType',
            'estado',
            'docNum',
            'motivoAnulacion',
            'origen',
            'usuario',
            'idUser',
        ],
    ]) ?>

</div>
