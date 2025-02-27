<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Empresa-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nombre"></span></div> <div class="col-md-6">    <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-direccion"></span></div> <div class="col-md-6">    <?= $form->field($model, 'telefono1')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-telefono1"></span></div> <div class="col-md-6">    <?= $form->field($model, 'telefono2')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-telefono2"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nit')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nit"></span></div> <div class="col-md-6">    <?= $form->field($model, 'pais')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-pais"></span></div> <div class="col-md-6">    <?= $form->field($model, 'ciudad')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-ciudad"></span></div> <div class="col-md-6">    <?= $form->field($model, 'actividad')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-actividad"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
