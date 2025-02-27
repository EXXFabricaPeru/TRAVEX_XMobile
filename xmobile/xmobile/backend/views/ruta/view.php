<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
?>
<span id="RutaID" data-id="<?= $id; ?>"></span>
<div class="row">
    <div class="col-md-4">
        <h4>Ruta</h4>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'nombre',
                'idvendedor',
                'fecha'
            ],
        ])
        ?>
    </div>    
</div>
