<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="poligonocabecera-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'usuario',
            'status',
            'dateUpdate',
        ],
    ]) ?>

</div>
