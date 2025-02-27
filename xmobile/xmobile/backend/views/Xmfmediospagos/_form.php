<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Xmfmediospagos-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'idCabecera')->textInput() ?><span class="text-danger text-clear" id="error-idCabecera"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nro_recibo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nro_recibo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'documentoId')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-documentoId"></span></div> <div class="col-md-6">    <?= $form->field($model, 'formaPago')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-formaPago"></span></div> <div class="col-md-6">    <?= $form->field($model, 'monto')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-monto"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numCheque')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-numCheque"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numComprobante')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-numComprobante"></span></div> <div class="col-md-6">    <?= $form->field($model, 'numTarjeta')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-numTarjeta"></span></div> <div class="col-md-6">    <?= $form->field($model, 'bancoCode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-bancoCode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'fecha')->textInput() ?><span class="text-danger text-clear" id="error-fecha"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cambio')->textInput() ?><span class="text-danger text-clear" id="error-cambio"></span></div> <div class="col-md-6">    <?= $form->field($model, 'monedaDolar')->textInput() ?><span class="text-danger text-clear" id="error-monedaDolar"></span></div> <div class="col-md-6">    <?= $form->field($model, 'monedaLocal')->textInput() ?><span class="text-danger text-clear" id="error-monedaLocal"></span></div> <div class="col-md-6">    <?= $form->field($model, 'centro')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-centro"></span></div> <div class="col-md-6">    <?= $form->field($model, 'baucher')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-baucher"></span></div> <div class="col-md-6">    <?= $form->field($model, 'checkdate')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-checkdate"></span></div> <div class="col-md-6">    <?= $form->field($model, 'transferencedate')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-transferencedate"></span></div> <div class="col-md-6">    <?= $form->field($model, 'CreditCard')->textInput() ?><span class="text-danger text-clear" id="error-CreditCard"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
