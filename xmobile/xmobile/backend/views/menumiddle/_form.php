<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Menumiddle-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'nombreMenu')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nombreMenu"></span></div> <div class="col-md-6">    <?= $form->field($model, 'seccion')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-seccion"></span></div> <div class="col-md-6">    <?= $form->field($model, 'estado')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-estado"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
