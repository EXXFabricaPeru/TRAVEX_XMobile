<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Rolex-form']); ?>
    <div class="row">
        <div class="col-md-6">    
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nombre"></span>
        </div> 
        <div class="col-md-6"><?= $form->field($model, 'tipo')->dropDownList(['web' => 'Web', 'movil' => 'Movil',], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-tipo"></span></div> 
        <div class="col-md-6"><?= $form->field($model, 'user')->textInput() ?>
            <span class="text-danger text-clear" id="error-user"></span></div>      
        <div class="col-md-6"><?= $form->field($model, 'descripcion')->textarea(['rows' => 6]) ?>
            <span class="text-danger text-clear" id="error-descripcion"></span>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
