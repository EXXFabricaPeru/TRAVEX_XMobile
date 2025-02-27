<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Sucursalalmacenes-form']); ?>
    <div class="row">
        <div class="col-md-12">   
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Almacenes::find()->all(), 'WarehouseCode', 'WarehouseName'); ?>
            <?= $form->field($model, 'almacenesId')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-almacenesId"></span></div> <div class="col-md-12"> 
            <?= $form->field($model, 'tiempo')->hiddenInput(['value' => date("Y-m-d H:m:s")])->label(false); ?>
            <?= $form->field($model, 'sucursalId')->hiddenInput()->label(false); ?>
            <span class="text-danger text-clear" id="error-tiempo"></span></div>   
    </div>


    <?php ActiveForm::end(); ?>

</div>
