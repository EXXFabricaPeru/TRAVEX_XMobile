<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="bancos-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codigo',
            'cuenta',
            'nombre',
        ],
    ]) ?>

</div>
