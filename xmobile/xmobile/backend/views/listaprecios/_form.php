<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Listaprecios-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'GroupNum')->textInput() ?><span class="text-danger text-clear" id="error-GroupNum"></span></div> <div class="col-md-6">    <?= $form->field($model, 'BasePriceList')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-BasePriceList"></span></div> <div class="col-md-6">    <?= $form->field($model, 'PriceListNo')->textInput() ?><span class="text-danger text-clear" id="error-PriceListNo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'PriceListName')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-PriceListName"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DefaultPrimeCurrency')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-DefaultPrimeCurrency"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
