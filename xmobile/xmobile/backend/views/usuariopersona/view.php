<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="usuariopersona-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idPersona',
            'nombrePersona',
            'apellidoPPersona',
            'apellidoMPersona',
            'estadoPersona',
            'fechaUMPersona',
            'documentoIdentidadPersona',
        ],
    ]) ?>

</div>
