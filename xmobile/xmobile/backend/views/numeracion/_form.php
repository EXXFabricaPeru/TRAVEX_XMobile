<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Numeracion-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'numcli')->textInput() ?><span class="text-danger text-clear" id="error-numcli"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numdof')->textInput() ?><span class="text-danger text-clear" id="error-numdof"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numdoe')->textInput() ?><span class="text-danger text-clear" id="error-numdoe"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numdfa')->textInput() ?><span class="text-danger text-clear" id="error-numdfa"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numdop')->textInput() ?><span class="text-danger text-clear" id="error-numdop"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numgp')->textInput() ?><span class="text-danger text-clear" id="error-numgp"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numgpa')->textInput() ?><span class="text-danger text-clear" id="error-numgpa"></span></div> <div class="col-md-6">    <?= $form->field($model, 'iduser')->textInput() ?><span class="text-danger text-clear" id="error-iduser"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
