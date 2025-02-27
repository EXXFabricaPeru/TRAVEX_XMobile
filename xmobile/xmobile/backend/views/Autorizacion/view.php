<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="autorizacion-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'autorizacion',
            'usuario',
            'accion',
        ],
    ]) ?>

</div>
