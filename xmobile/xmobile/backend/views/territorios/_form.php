<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Territorios-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'TerritoryID')->textInput() ?><span class="text-danger text-clear" id="error-TerritoryID"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Description')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Description"></span></div> <div class="col-md-6">    <?= $form->field($model, 'LocationIndex')->textInput() ?><span class="text-danger text-clear" id="error-LocationIndex"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Inactive')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Inactive"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Parent')->textInput() ?><span class="text-danger text-clear" id="error-Parent"></span></div> <div class="col-md-6">    <?= $form->field($model, 'User')->textInput() ?><span class="text-danger text-clear" id="error-User"></span></div> <div class="col-md-6">    <?= $form->field($model, 'Status')->textInput() ?><span class="text-danger text-clear" id="error-Status"></span></div> <div class="col-md-6">    <?= $form->field($model, 'DateUpdate')->textInput() ?><span class="text-danger text-clear" id="error-DateUpdate"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
