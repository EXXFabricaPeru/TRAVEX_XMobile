<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Listacamposusuarios-form']); ?>
    <div class="row">
        <?= $form->field($model, 'IdcampoUsuario')->hiddenInput()->label(false); ?>
    <div class="col-md-6">    
        <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
        <span class="text-danger text-clear" id="error-codigo"></span>
    </div> 
    <div class="col-md-12">    
        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        <span class="text-danger text-clear" id="error-nombre"></span>
    </div> 
    <div class="col-md-6">    
            <?= $form->field($model, 'Status')->dropDownList(['1' => 'Activo', '0' => 'Inactivo'], ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-Status"></span>
    </div> 
</div>


    <?php ActiveForm::end(); ?>

</div>
