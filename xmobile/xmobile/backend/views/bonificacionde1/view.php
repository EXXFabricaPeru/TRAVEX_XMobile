<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="bonificacionde1-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Code',
            'Name',
            'U_ID_bonificacion',
            'U_regla',
        ],
    ]) ?>

</div>
