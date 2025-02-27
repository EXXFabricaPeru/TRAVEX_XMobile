<?php
use yii\helpers\Html;
?>
<div class="configuracion-create">
    <?= $this->render('_form', [
        'model' => $model,
        'crear' => true,
        'valor' => $valor,
        'valor2' => $valor2
    ]) ?>
</div>
