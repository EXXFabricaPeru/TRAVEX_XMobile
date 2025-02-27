<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="numeracion-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'numcli',
            'numdof',
            'numdoe',
            'numdfa',
            'numdop',
            'numgp',
            'numgpa',
            'numccaja',
            'iduser',
        ],
    ]) ?>

</div>
