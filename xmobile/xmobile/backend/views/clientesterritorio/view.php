<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="clientesterritorio-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'CardCode',
            'CardName',
            'TerritoryId',
            'TerritoryName',
        ],
    ]) ?>

</div>
