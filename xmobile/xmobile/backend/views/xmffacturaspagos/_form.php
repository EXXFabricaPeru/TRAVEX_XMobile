<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Xmffacturaspagos-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'idCabecera')->textInput() ?><span class="text-danger text-clear" id="error-idCabecera"></span></div> <div class="col-md-6">    <?= $form->field($model, 'clienteId')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-clienteId"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nro_recibo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nro_recibo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'documentoId')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-documentoId"></span></div> <div class="col-md-6">    <?= $form->field($model, 'docentry')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-docentry"></span></div> <div class="col-md-6">    <?= $form->field($model, 'monto')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-monto"></span></div> <div class="col-md-6">    <?= $form->field($model, 'CardName')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-CardName"></span></div> <div class="col-md-6">    <?= $form->field($model, 'saldo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-saldo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nroFactura')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nroFactura"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DocTotal')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-DocTotal"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cuota')->textInput() ?><span class="text-danger text-clear" id="error-cuota"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
