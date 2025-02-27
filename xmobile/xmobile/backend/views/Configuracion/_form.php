<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Configuracion-form']); ?>
    <div class="row">
        <?php if (isset($crear)) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'parametro')->dropDownList(['giftcard' => 'giftcard', 'combo' => 'combo'],['onchange' => 'cambiarParametroCombo();']) ?>
            </div>
        <?php } ?>
        <?php if (!isset($crear)) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'parametro')->textInput(['maxlength' => true, 'disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-parametro"></span>
            </div>
        <?php } ?>
        <?php if ($valor == true) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'valor')->textInput() ?><span class="text-danger text-clear" id="error-valor"></span>
            </div>
        <?php } ?>
        <?php if ($valor == false) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'valor')->textInput(['disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-valor"></span>
            </div>
        <?php } ?>
        <div class="col-md-6">
            <?= $form->field($model, 'especificacion')->textInput(['maxlength' => true, 'disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-especificacion"></span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'estado')->textInput(['disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-estado"></span>
        </div>
        <?php if ($valor2 == true) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'valor2')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-valor2"></span>
            </div>
        <?php } ?>
        <?php if ($valor2 == false) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'valor2')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-valor2"></span>
            </div>
        <?php } ?>
        <div class="col-md-6">
            <?= $form->field($model, 'valor3')->textInput(['maxlength' => true, 'disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-valor3"></span>
        </div>
        <div class="col-md-6">    
            <?= $form->field($model, 'valor4')->textInput(['maxlength' => true, 'disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-valor4"></span>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

