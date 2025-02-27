<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="grupoproductodocificacion-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
        ],
    ]) ?>

</div>
