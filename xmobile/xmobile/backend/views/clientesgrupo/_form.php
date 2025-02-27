<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Clientesgrupo-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Code"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Name"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Type')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Type"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput() ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
