<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
?>
<span id="MotivoanulacionID" data-id="<?= $id; ?>"></span>
<div class="row">
    <div class="col-md-4">
        <h4>Motivos</h4>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'Code',
                'Name',
                'U_TipoAnulacion',
            ],
        ])
        ?>
    </div>    
</div>
