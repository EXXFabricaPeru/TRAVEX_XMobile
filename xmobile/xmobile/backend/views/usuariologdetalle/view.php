<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="usuariolog-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fechaIngreso',
            'fecha',
            'usuario',
            'idUsuario',
            'ipAddress',
            'codigo',
        ],
    ]) ?>

</div>
