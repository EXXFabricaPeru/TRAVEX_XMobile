<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Cuentascontables-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Code"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Name"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Balance')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Balance"></span></div> <div class="col-md-6">    <?= $form->field($model, 'AccountLevel')->textInput() ?><span class="text-danger text-clear" id="error-AccountLevel"></span></div> <div class="col-md-6">    <?= $form->field($model, 'FatherAccountKey')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-FatherAccountKey"></span></div> <div class="col-md-6">    <?= $form->field($model, 'AcctCurrency')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-AcctCurrency"></span></div> <div class="col-md-6">    <?= $form->field($model, 'FormatCode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-FormatCode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput() ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
