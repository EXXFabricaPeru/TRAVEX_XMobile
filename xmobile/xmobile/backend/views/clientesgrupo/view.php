<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="clientesgrupo-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Code',
            'Name',
            'Type',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
