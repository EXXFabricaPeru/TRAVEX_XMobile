<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Usuariolog-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'fechaIngreso')->textInput() ?><span class="text-danger text-clear" id="error-fechaIngreso"></span></div> <div class="col-md-6">    <?= $form->field($model, 'fecha')->textInput() ?><span class="text-danger text-clear" id="error-fecha"></span></div> <div class="col-md-6">    <?= $form->field($model, 'usuario')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-usuario"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idUsuario')->textInput() ?><span class="text-danger text-clear" id="error-idUsuario"></span></div> <div class="col-md-6">    <?= $form->field($model, 'ipAddress')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-ipAddress"></span></div> <div class="col-md-6">    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-codigo"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
