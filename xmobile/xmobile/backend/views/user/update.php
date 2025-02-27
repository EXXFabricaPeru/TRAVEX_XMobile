<?php

use yii\helpers\Html;
?>
<div class="user-update">
    <?=
    $this->render('_form', [
        'model' => $model,
        'up' => true,
    ])
    ?>
</div>
