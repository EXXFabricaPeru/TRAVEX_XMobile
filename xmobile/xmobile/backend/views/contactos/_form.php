<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Contactos-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cardCode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-cardCode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nombre"></span></div> <div class="col-md-6">    <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-direccion"></span></div> <div class="col-md-6">    <?= $form->field($model, 'telefono1')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-telefono1"></span></div> <div class="col-md-6">    <?= $form->field($model, 'telefono2')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-telefono2"></span></div> <div class="col-md-6">    <?= $form->field($model, 'celular')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-celular"></span></div> <div class="col-md-6">    <?= $form->field($model, 'tipo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-tipo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'comentarios')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-comentarios"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput() ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput() ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div> <div class="col-md-6">    <?= $form->field($model, 'correo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-correo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-titulo"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
