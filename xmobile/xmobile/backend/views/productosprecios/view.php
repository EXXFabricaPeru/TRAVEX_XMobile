<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="productosprecios-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ItemCode',
            'IdListaPrecios',
            'IdUnidadMedida',
            'Price',
            'Currency',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
