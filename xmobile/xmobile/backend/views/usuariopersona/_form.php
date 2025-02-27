<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Usuariopersona-form']); ?>
    <div class="row">
        <div class="col-md-10">    
            <?= $form->field($model, 'nombrePersona')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombrePersona"></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">  
            <?= $form->field($model, 'apellidoPPersona')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-apellidoPPersona"></span>
        </div> 
        <div class="col-md-6">  
            <?= $form->field($model, 'apellidoMPersona')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-apellidoMPersona"></span>
        </div> 
        <div class="col-md-5">  
            <?= $form->field($model, 'documentoIdentidadPersona')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-documentoIdentidadPersona"></span>
            <?= $form->field($model, 'estadoPersona')->hiddenInput(['value' => 1, 'type' => 'hidden'])->label(false); ?>
            <?= $form->field($model, 'fechaUMPersona')->hiddenInput(['value' => date('Y-m-d H:m:s')])->label(false); ?>
        </div>     
    </div>
    <?php ActiveForm::end(); ?>

</div>
