<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Anulaciondocmovil-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'fechaRegistro')->textInput() ?><span class="text-danger text-clear" id="error-fechaRegistro"></span></div> <div class="col-md-6">    <?= $form->field($model, 'usuario')->textInput() ?><span class="text-danger text-clear" id="error-usuario"></span></div> <div class="col-md-6">    <?= $form->field($model, 'docDate')->textInput() ?><span class="text-danger text-clear" id="error-docDate"></span></div> <div class="col-md-6">    <?= $form->field($model, 'docType')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-docType"></span></div> <div class="col-md-6">    <?= $form->field($model, 'docEntry')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-docEntry"></span></div> <div class="col-md-6">    <?= $form->field($model, 'motivoAnulacion')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-motivoAnulacion"></span></div> <div class="col-md-6">    <?= $form->field($model, 'estado')->textInput() ?><span class="text-danger text-clear" id="error-estado"></span></div> <div class="col-md-6">    <?= $form->field($model, 'idUser')->textInput() ?><span class="text-danger text-clear" id="error-idUser"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
