<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Usuariosincronizamovil-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'fecha')->textInput() ?><span class="text-danger text-clear" id="error-fecha"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idUsuario')->textInput() ?><span class="text-danger text-clear" id="error-idUsuario"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idSucursal')->textInput() ?><span class="text-danger text-clear" id="error-idSucursal"></span></div> <div class="col-md-6">    <?= $form->field($model, 'equipo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-equipo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'servicio')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-servicio"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
