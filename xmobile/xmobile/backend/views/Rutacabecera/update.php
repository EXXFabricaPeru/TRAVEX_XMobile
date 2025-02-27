<?php
use yii\helpers\Html;
?>
<div class="rutacabecera-update">
    <?= $this->render('_form', [
        'model' => $model,
        'detalle' => $detalle
    ])
    ?>
</div>
