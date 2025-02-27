<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="territorios-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'TerritoryID',
            'Description',
            'LocationIndex',
            'Inactive',
            'Parent',
            'User',
            'Status',
            'DateUpdate',
        ],
    ]) ?>

</div>
