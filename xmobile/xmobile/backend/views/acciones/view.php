<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="acciones-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cod',
            'nombre',
        ],
    ]) ?>

</div>
