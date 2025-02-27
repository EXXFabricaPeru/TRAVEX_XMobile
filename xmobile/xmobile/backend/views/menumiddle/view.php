<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="menumiddle-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombreMenu',
            'seccion',
            'estado',
        ],
    ]) ?>

</div>
