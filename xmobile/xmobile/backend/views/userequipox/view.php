<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="userequipox-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'userId',
            'equipoxId',
            'tiempo',
        ],
    ]) ?>

</div>
