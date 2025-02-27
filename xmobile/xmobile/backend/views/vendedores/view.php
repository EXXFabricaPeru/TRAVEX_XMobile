<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="vendedores-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'SalesEmployeeCode',
            'SalesEmployeeName',
            'EmployeeId',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
