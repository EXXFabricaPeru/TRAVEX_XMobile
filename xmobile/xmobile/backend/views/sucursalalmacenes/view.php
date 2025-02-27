<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="sucursalalmacenes-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sucursalId',
            'almacenesId',
            'tiempo',
        ],
    ]) ?>

</div>
