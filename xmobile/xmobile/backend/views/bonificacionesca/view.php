<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="bonificacionesca-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Code',
            'Name',
            'U_tipo',
            'U_cliente',
            'U_fecha',
            'U_fecha_inicio',
            'U_fecha_fin',
            'U_estado',
            'U_limitemaxregalo',
            'U_cantidadbonificacion',
            'U_observacion',
            'U_reglatipo',
            'U_reglaunidad',
            'U_reglacantidad',
            'U_bonificaciontipo',
            'U_bonificacionunidad',
            'U_bonificacioncantidad',
        ],
    ]) ?>

</div>
