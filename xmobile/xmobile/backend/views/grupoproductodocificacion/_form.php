<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Grupoproductodocificacion-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-nombre"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
