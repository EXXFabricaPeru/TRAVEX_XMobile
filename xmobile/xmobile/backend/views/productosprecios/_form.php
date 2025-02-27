<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Productosprecios-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'ItemCode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-ItemCode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'IdListaPrecios')->textInput() ?><span class="text-danger text-clear" id="error-IdListaPrecios"></span></div> <div class="col-md-6">    <?= $form->field($model, 'IdUnidadMedida')->textInput() ?><span class="text-danger text-clear" id="error-IdUnidadMedida"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Price')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Price"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Currency')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Currency"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput() ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput() ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
