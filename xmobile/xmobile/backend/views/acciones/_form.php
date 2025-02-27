<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Acciones-form']); ?>
    <div class="row">
        <div class="col-md-12"> 
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-cod"></span></div> <div class="col-md-12">  
            <?= $form->field($model, 'cod')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombre"></span></div>    
    </div>


    <?php ActiveForm::end(); ?>

</div>
