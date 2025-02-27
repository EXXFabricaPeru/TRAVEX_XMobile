<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="tienex-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'rolexId',
            'userId',
            'accionesId',
            'descripcion',
        ],
    ]) ?>

</div>
