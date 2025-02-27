<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Equipox-form']); ?>
    <div class="row">
        <div class="col-md-6">    
            <?= $form->field($model, 'equipo')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-equipo"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'uuid')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-uuid"></span>
        </div> 
    </div>
    <div class="row">
        <div class="col-md-6">  
            <?= $form->field($model, 'keyid')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-keyid"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'plataforma')->dropDownList(['Android' => 'Android', 'IOS' => 'IOS', 'Web' => 'Web'], ['prompt' => '']) ?>
            <span class="text-danger text-clear" id="error-plataforma"></span>
        </div> 
    </div> 
    <div class="row">
        <div class="col-md-6">  
            <?= $form->field($model, 'estado')->dropDownList(['Activo' => 'Activo', 'Inactivo' => 'Inactivo',], ['prompt' => '']) ?>

            <span class="text-danger text-clear" id="error-registrado"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'version')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-version"></span>

        </div> 
    </div> 
    <div class="row">
        <div class="col-md-6">   
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Sucursalx::find()->all(), 'id', 'nombre'); ?>
            <?= $form->field($model, 'sucursalxId')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-sucursalxId"></span>
            <?= $form->field($model, 'registrado')->hiddenInput(['value' => date('Y-m-d H:m:s')])->label(false); ?>
        </div>
        <div class="col-md-6">  
            <?= $form->field($model, 'fex')->dropDownList(['0' => 'No Usa', '1' => 'Utiliza']) ?>

            <span class="text-danger text-clear" id="error-registrado"></span>
        </div>   
    </div>
    <?php ActiveForm::end(); ?>
</div>
