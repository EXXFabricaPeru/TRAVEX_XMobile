<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Tipopapel-form']); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombre"></span>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-descripcion"></span></div>
        </div>
        <div class="col-md-12">
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Formatopapel::find()->all(), 'id', 'descripcion'); ?>
            <?= $form->field($model, 'formato')->dropDownList($arr); ?>
            <span class="text-danger text-clear" id="error-formato"></span>
        </div>
    <?php ActiveForm::end(); ?>
</div>
