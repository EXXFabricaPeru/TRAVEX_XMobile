<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Vendedores-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'SalesEmployeeCode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-SalesEmployeeCode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'SalesEmployeeName')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-SalesEmployeeName"></span></div> <div class="col-md-6">    <?= $form->field($model, 'EmployeeId')->textInput() ?><span class="text-danger text-clear" id="error-EmployeeId"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
