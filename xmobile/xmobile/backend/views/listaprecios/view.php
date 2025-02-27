<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="listaprecios-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'GroupNum',
            'BasePriceList',
            'PriceListNo',
            'PriceListName',
            'DefaultPrimeCurrency',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
