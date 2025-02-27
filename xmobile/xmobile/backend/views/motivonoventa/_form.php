<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Motivonoventa-form']); ?>
    <div class="row">
        <div class="col-md-12">   
            <div class="col-md-6">   
                <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
                <span class="text-danger text-clear" id="error-Code"></span>
            </div> 
            <div class="col-md-6">  
                <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
                <span class="text-danger text-clear" id="error-Name"></span></div>
            </div>
        </div>
        <div class="col-md-12">  
            <?= $form->field($model, 'Razon')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-Razon"></span></div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
