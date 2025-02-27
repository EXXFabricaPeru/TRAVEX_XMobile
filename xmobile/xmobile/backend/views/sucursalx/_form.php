<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Sucursalx-form']); ?>
    <div class="row">
        <div class="col-md-6">   
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombre"></span>
        </div> <div class="col-md-6">   
            <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-direccion"></span></div> 

        <div class="col-md-6">  
            <?= $form->field($model, 'empresa')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-empresa"></span></div> <div class="col-md-6">   
            <?= $form->field($model, 'telefonouno')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-telefonouno"></span></div> 
        <div class="col-md-6">  
            <?= $form->field($model, 'telefonodos')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-telefonodos"></span></div> <div class="col-md-6">
            <?= $form->field($model, 'pais')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-pais">
            </span>
        </div> 
        <div class="col-md-6">   
            <?= $form->field($model, 'ciudad')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-ciudad"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'leyendados')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-leyendados"></span></div> 
        <div class="col-md-6">  
            <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-codigo"></span>
        </div>
        <div class="col-md-12">   
            <?= $form->field($model, 'descripcion')->textarea(['rows' => 6]) ?>
            <span class="text-danger text-clear" id="error-descripcion"></span>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>
