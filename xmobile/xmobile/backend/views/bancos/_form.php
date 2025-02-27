<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Bancos-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-codigo"></span></div> <div class="col-md-6">    <?= $form->field($model, 'cuenta')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-cuenta"></span></div> <div class="col-md-6">    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nombre"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
