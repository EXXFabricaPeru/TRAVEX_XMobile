<?php
use yii\helpers\Html;
?>
<div class="configuracion-update">
    <?= $this->render('_form', [
        'model' => $model,
        'valor' => $valor,
        'valor2' => $valor2
    ]) ?>
</div>
