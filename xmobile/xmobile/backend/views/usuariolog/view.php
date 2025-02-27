<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="usuariolog-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fechaIngreso',
            'usuario',
            'ipAddress',
            'codigo',
        ],
    ]) ?>

</div>
