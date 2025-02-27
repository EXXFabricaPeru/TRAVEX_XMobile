<?php
use yii\helpers\Html;
?>
<div class="lbcc-update">
    <?= $this->render('_form', [
        'model' => $model,
        'grupoCliente' => $grupoCliente,
        'grupoProducto' => $grupoProducto
    ]) ?>
</div>
