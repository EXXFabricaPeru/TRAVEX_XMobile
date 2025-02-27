<?php
use yii\helpers\Html;
?>
<div class="lbcc-create">
    <?= $this->render('_form', [
        'model' => $model,
        'grupoCliente' => $grupoCliente,
        'grupoProducto' => $grupoProducto
    ]) ?>
</div>
