<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="almacenes-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Street',
            'WarehouseCode',
            'State',
            'Country',
            'City',
            'WarehouseName',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
