<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Configuracionesgenerales-form']); ?>
    <div class="row">
        <div class="col-md-12"> 
            <?php
            $code = '0';
            (isset($model->code)) ? $code = $model->code : $code = date('U');
            ?>
            <?= $form->field($model, 'code')->textInput(['value' => $code, 'readonly' => true]) ?>
            <span class="text-danger text-clear" id="error-precio"></span>
        </div>  
        <div class="col-md-12">    
            <?= $form->field($model, 'empresa')->textInput() ?>
            <span class="text-danger text-clear" id="error-empresa"></span>
        </div> 
        <div class="col-md-12">    
            <?= $form->field($model, 'precio')->dropDownList(['SI' => 'SI', 'NO' => 'NO',], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-precio"></span>
        </div> 
        <div class="col-md-12">    
            <?= $form->field($model, 'bonificacion')->dropDownList(['SI' => 'SI', 'NO' => 'NO',], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-bonificacion"></span>
        </div> 
        <div class="col-md-12">    <?= $form->field($model, 'grupoproductos')->dropDownList(['SI' => 'SI', 'NO' => 'NO',], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-grupoproductos"></span>
        </div> 
        <div class="col-md-12">    
            <?= $form->field($model, 'grupoclientes')->dropDownList(['SI' => 'SI', 'NO' => 'NO',], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-grupoclientes"></span>
        </div> 
        <div class="col-md-12">   
            <?= $form->field($model, 'docificacion')->dropDownList(['SI' => 'SI', 'NO' => 'NO',], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-docificacion"></span>
        </div>        
    </div>
    <?php ActiveForm::end(); ?>
</div>
