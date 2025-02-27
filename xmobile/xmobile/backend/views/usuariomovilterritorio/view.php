<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="usuariomovilterritorio-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idUser',
            'user',
            'idTerritorio',
            'territorio',
            'idUserRegister',
            'userRegister',
            'fechaSistema',
        ],
    ]) ?>

</div>
