<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="permisosmiddle-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idUsuario',
            'userName',
            'nivel',
            'descripcionNivel',
            'departamento',
            'idCargoEmpresa',
            'cargoEmpresa',
            'permisomenu',
        ],
    ]) ?>

</div>
