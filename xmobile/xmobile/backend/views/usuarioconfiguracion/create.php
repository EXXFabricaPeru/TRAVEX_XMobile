<?php

use yii\helpers\Html;
?>
<div class="usuarioconfiguracion-create">
    <?=
    $this->render('_form', [
        'model' => $model,
        'exitx' => 0,
        'condiciones' => $condiciones,
        'cc' => [],
        'verCC' => $verCC
    ])
    ?>
</div>
