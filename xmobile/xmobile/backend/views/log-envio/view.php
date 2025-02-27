<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="log-envio-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idlog',
            'proceso',
            'envio:ntext',
            'respuesta:ntext',
            'fecha',
            'ultimo',
            'endpoint',
            'documento',
        ],
    ]) ?>

</div>
