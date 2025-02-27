<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Xmfcabezerapagos-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'nro_recibo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nro_recibo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'correlativo')->textInput() ?><span class="text-danger text-clear" id="error-correlativo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'usuario')->textInput() ?><span class="text-danger text-clear" id="error-usuario"></span></div> <div class="col-md-6">    <?= $form->field($model, 'documentoId')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-documentoId"></span></div> <div class="col-md-6">    <?= $form->field($model, 'fecha')->textInput() ?><span class="text-danger text-clear" id="error-fecha"></span></div> <div class="col-md-6">    <?= $form->field($model, 'hora')->textInput() ?><span class="text-danger text-clear" id="error-hora"></span></div> <div class="col-md-6">    <?= $form->field($model, 'monto_total')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-monto_total"></span></div> <div class="col-md-6">    <?= $form->field($model, 'tipo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-tipo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'otpp')->textInput() ?><span class="text-danger text-clear" id="error-otpp"></span></div> <div class="col-md-6">    <?= $form->field($model, 'tipo_cambio')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-tipo_cambio"></span></div> <div class="col-md-6">    <?= $form->field($model, 'moneda')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-moneda"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cliente_carcode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-cliente_carcode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'razon_social')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-razon_social"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nit')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nit"></span></div> <div class="col-md-6">    <?= $form->field($model, 'estado')->textInput() ?><span class="text-danger text-clear" id="error-estado"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cancelado')->textInput() ?><span class="text-danger text-clear" id="error-cancelado"></span></div> <div class="col-md-6">    <?= $form->field($model, 'tipoTarjeta')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-tipoTarjeta"></span></div> <div class="col-md-6">    <?= $form->field($model, 'fechaSistema')->textInput() ?><span class="text-danger text-clear" id="error-fechaSistema"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idDocumento')->textInput() ?><span class="text-danger text-clear" id="error-idDocumento"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
