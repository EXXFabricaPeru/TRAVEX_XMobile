<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\i18n\Formatter;
use yii\base\Widget;
use yii\jui\DatePicker;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Bonificacionde1-form']); ?>
    <div class="row">
    <!--<div class="col-md-6">    
        <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
        <span class="text-danger text-clear" id="error-Code"></span>
    </div>-->
    <div class="col-md-6"> 
        <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        <span class="text-danger text-clear" id="error-Name"></span>
    </div> 
    <!--<div class="col-md-6">    
        <?= $form->field($model, 'U_ID_bonificacion')->textInput(['maxlength' => true]) ?>
        <span class="text-danger text-clear" id="error-U_ID_bonificacion"></span>
    </div> -->
    <div class="col-md-6">    
        <?= $form->field($model, 'U_regla')->textInput(['maxlength' => true]) ?>
        <span class="text-danger text-clear" id="error-U_regla"></span></div>       
    </div>


    <?php ActiveForm::end(); ?>

</div>
