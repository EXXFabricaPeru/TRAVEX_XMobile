<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Motivoanulacion-form']); ?>
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
            <?= $form->field($model, 'U_TipoAnulacion')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-U_TipoAnulacion"></span></div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
