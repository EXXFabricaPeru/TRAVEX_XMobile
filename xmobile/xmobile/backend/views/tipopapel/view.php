<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="tipopapel-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'descripcion',
        ],
    ]) ?>

</div>
