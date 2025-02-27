<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="cuentascontables-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Code',
            'Name',
            'Balance',
            'AccountLevel',
            'FatherAccountKey',
            'AcctCurrency',
            'FormatCode',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
