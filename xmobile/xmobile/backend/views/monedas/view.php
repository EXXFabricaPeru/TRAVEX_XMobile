<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="monedas-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Code',
            'Name',
            'DocumentsCode',
            'Type',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
