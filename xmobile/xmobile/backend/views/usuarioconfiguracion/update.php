<?php

use yii\helpers\Html;
?>
<div class="usuarioconfiguracion-update">
    <?=
    $this->render('_form', [
        'model' => $model,
        'exitx' => $exitx,
        'condiciones' => $condiciones,
        'textoCondiciones' => $textoCondiciones,
        'cc' => $cc,
        'verCC' => $verCC
    ])
    ?>
</div>
