<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Log Envio-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'proceso')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-proceso"></span></div> <div class="col-md-6">    <?= $form->field($model, 'envio')->textarea(['rows' => 6]) ?><span class="text-danger text-clear" id="error-envio"></span></div> <div class="col-md-6">    <?= $form->field($model, 'respuesta')->textarea(['rows' => 6]) ?><span class="text-danger text-clear" id="error-respuesta"></span></div> <div class="col-md-6">    <?= $form->field($model, 'fecha')->textInput() ?><span class="text-danger text-clear" id="error-fecha"></span></div> <div class="col-md-6">    <?= $form->field($model, 'ultimo')->textInput() ?><span class="text-danger text-clear" id="error-ultimo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'endpoint')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-endpoint"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
