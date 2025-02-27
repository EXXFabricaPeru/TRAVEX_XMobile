<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Almacenes-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'Street')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Street"></span></div> <div class="col-md-6">    <?= $form->field($model, 'WarehouseCode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-WarehouseCode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'State')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-State"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Country')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Country"></span></div> <div class="col-md-6">    <?= $form->field($model, 'City')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-City"></span></div> <div class="col-md-6">    <?= $form->field($model, 'WarehouseName')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-WarehouseName"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
