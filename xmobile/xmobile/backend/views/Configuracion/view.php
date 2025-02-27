<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="configuracion-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parametro',
            'valor',
            'especificacion',
            'estado',
            'valor2',
            'valor3',
            'valor4',
        ],
    ]) ?>

</div>
