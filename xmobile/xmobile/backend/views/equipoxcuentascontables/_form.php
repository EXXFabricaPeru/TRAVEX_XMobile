<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$arr = \yii\helpers\ArrayHelper::map(backend\models\Cuentascontables::find()->orderby('Code asc')->all(), 'Code', 'Name');
$arrClientes = \yii\helpers\ArrayHelper::map(backend\models\Cuentascontables::find()->where("FatherAccountKey='1.2.01.01.000'")->all(), 'Code', 'Name');

foreach ($arr as $key => $value) {
	$arr[$key]=$key.' - '.$value;
}

foreach ($arrClientes as $key => $value) {
    $arrClientes[$key]=$key.' - '.$value;
}
//print_r($arr);
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Equipoxcuentascontables-form']); ?>
    <p class="alert alert-info text-info">Cuentas Clientes por Region</p>
    <div class="row">
        <div class="col-md-4">  
            <?= $form->field($model, 'cuentaClientesRegion')->dropDownList($arrClientes, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaClientesRegion"></span>

        </div>
    </div>
    <p class="alert alert-info text-info">Cuentas en moneda Local</p>
    <?= $form->field($model, 'equipoxId')->hiddenInput(['value' => Yii::$app->session->get('IDEQUIPO')])->label(false); ?>  
    <span class="text-danger text-clear" id="error-equipoxId"></span>
    <div class="row">
        <div class="col-md-4">  
            <?= $form->field($model, 'cuentaEfectivo')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaEfectivo"></span>

        </div> 
        <div class="col-md-4">  
            <?= $form->field($model, 'cuentaCheque')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaCheque"></span></div> <div class="col-md-4">   
            <?= $form->field($model, 'cuentaTranferencia')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaTranferencia"></span></div> <div class="col-md-4">   
            <?= $form->field($model, 'cuentaTarjeta')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaTarjeta"></span></div> <div class="col-md-4"> 
            <?= $form->field($model, 'cuentaAnticipos')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaAnticipos"></span></div>
    </div>
    <p  class="alert alert-info text-info">Cuentas en moneda Extrangera</p>
    <div class="row">
        <div class="col-md-4">  
            <?= $form->field($model, 'cuentaEfectivoUSD')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaEfectivoUSD"></span></div> <div class="col-md-4"> 
            <?= $form->field($model, 'cuentaChequeUSD')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaChequeUSD"></span></div> <div class="col-md-4">   
            <?= $form->field($model, 'cuentaTranferenciaUSD')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaTranferenciaUSD"></span></div> <div class="col-md-4">  
            <?= $form->field($model, 'cuentaTarjetaUSD')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaTarjetaUSD"></span></div> <div class="col-md-4">   
            <?= $form->field($model, 'cuentaAnticiposUSD')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-cuentaAnticiposUSD"></span></div>       
    </div>


    <?php ActiveForm::end(); ?>

</div>
