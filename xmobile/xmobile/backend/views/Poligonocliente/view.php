<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="poligonocliente-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cardcode',
            'cardname',
            'latitud',
            'longitud',
            'territoryid',
            'territoryname',
            'poligonoid',
            'poligononombre',
            'posicion',
            'dia',
        ],
    ]) ?>

</div>
