<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Geolocalizacion-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idequipox')->textInput() ?><span class="text-danger text-clear" id="error-idequipox"></span></div> <div class="col-md-6">    <?= $form->field($model, 'latitud')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-latitud"></span></div> <div class="col-md-6">    <?= $form->field($model, 'longitud')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-longitud"></span></div> <div class="col-md-6">    <?= $form->field($model, 'fecha')->textInput() ?><span class="text-danger text-clear" id="error-fecha"></span></div> <div class="col-md-6">    <?= $form->field($model, 'hora')->textInput() ?><span class="text-danger text-clear" id="error-hora"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idcliente')->textInput() ?><span class="text-danger text-clear" id="error-idcliente"></span></div> <div class="col-md-6">    <?= $form->field($model, 'documentocod')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-documentocod"></span></div> <div class="col-md-6">    <?= $form->field($model, 'tipodoc')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-tipodoc"></span></div> <div class="col-md-6">    <?= $form->field($model, 'estado')->textInput() ?><span class="text-danger text-clear" id="error-estado"></span></div> <div class="col-md-6">    <?= $form->field($model, 'actividad')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-actividad"></span></div> <div class="col-md-6">    <?= $form->field($model, 'anexo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-anexo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'usuario')->textInput() ?><span class="text-danger text-clear" id="error-usuario"></span></div> <div class="col-md-6">    <?= $form->field($model, 'status')->textInput() ?><span class="text-danger text-clear" id="error-status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'dateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-dateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
