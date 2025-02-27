<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Permisosx-form']); ?>
    <div class="row">
 <div class="col-md-6">    <?= $form->field($model, 'id')->textInput() ?><span class="text-danger text-clear" id="error-id"></span></div> <div class="col-md-6">    <?= $form->field($model, 'rolexId')->textInput() ?><span class="text-danger text-clear" id="error-rolexId"></span></div> <div class="col-md-6">    <?= $form->field($model, 'accionesId')->textInput() ?><span class="text-danger text-clear" id="error-accionesId"></span></div>        </div>


    <?php ActiveForm::end(); ?>

</div>
