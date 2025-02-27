<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="permisosx-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'rolexId',
            'accionesId',
        ],
    ]) ?>

</div>
