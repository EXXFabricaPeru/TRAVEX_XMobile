<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="listacamposusuarios-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'IdcampoUsuario',
            'codigo',
            'nombre',
            'Status',
        ],
    ]) ?>

</div>
