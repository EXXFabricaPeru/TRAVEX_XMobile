<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Poligonocliente-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'cardcode')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-cardcode"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cardname')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-cardname"></span></div> <div class="col-md-6">    <?= $form->field($model, 'territoryid')->textInput() ?><span class="text-danger text-clear" id="error-territoryid"></span></div> <div class="col-md-6">    <?= $form->field($model, 'territoryname')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-territoryname"></span></div> <div class="col-md-6">    <?= $form->field($model, 'poligonoid')->textInput() ?><span class="text-danger text-clear" id="error-poligonoid"></span></div> <div class="col-md-6">    <?= $form->field($model, 'poligononombre')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-poligononombre"></span></div> <div class="col-md-6">    <?= $form->field($model, 'posicion')->textInput() ?><span class="text-danger text-clear" id="error-posicion"></span></div> <div class="col-md-6">    <?= $form->field($model, 'dia')->textInput() ?><span class="text-danger text-clear" id="error-dia"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
