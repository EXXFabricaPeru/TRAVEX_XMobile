<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Cabeceradocumentos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cabeceradocumentos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'DocEntry')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocNum')->textInput() ?>

    <?= $form->field($model, 'DocType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'canceled')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Printed')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocStatus')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocDate')->textInput() ?>

    <?= $form->field($model, 'DocDueDate')->textInput() ?>

    <?= $form->field($model, 'CardCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CardName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'NumAtCard')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DiscPrcnt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DiscSum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocCur')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocRate')->textInput() ?>

    <?= $form->field($model, 'DocTotal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PaidToDate')->textInput() ?>

    <?= $form->field($model, 'Ref1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Ref2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Comments')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'JrnlMemo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'GroupNum')->textInput() ?>

    <?= $form->field($model, 'SlpCode')->textInput() ?>

    <?= $form->field($model, 'Series')->textInput() ?>

    <?= $form->field($model, 'TaxDate')->textInput() ?>

    <?= $form->field($model, 'LicTradNum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'UserSign')->textInput() ?>

    <?= $form->field($model, 'CreateDate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'UserSign2')->textInput() ?>

    <?= $form->field($model, 'UpdateDate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4MOTIVOCANCELADO')->textInput() ?>

    <?= $form->field($model, 'U_4NIT')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4RAZON_SOCIAL')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_LATITUD')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_LONGITUD')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4SUBTOTAL')->textInput() ?>

    <?= $form->field($model, 'U_4DOCUMENTOORIGEN')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4MIGRADOCONCEPTO')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4MIGRADO')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PriceListNum')->textInput() ?>

    <?= $form->field($model, 'estadosend')->textInput() ?>

    <?= $form->field($model, 'fecharegistro')->textInput() ?>

    <?= $form->field($model, 'fechaupdate')->textInput() ?>

    <?= $form->field($model, 'fechasend')->textInput() ?>

    <?= $form->field($model, 'idDocPedido')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idUser')->textInput() ?>

    <?= $form->field($model, 'estado')->textInput() ?>

    <?= $form->field($model, 'gestion')->textInput() ?>

    <?= $form->field($model, 'mes')->textInput() ?>

    <?= $form->field($model, 'correlativo')->textInput() ?>

    <?= $form->field($model, 'rowNum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'UNumFactura')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ControlCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Indicator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ShipToCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ControlAccount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_LB_NumeroFactura')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_LB_EstadoFactura')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_LB_NumeroAutorizac')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_LB_TipoFactura')->textInput() ?>

    <?= $form->field($model, 'U_LB_TotalNCND')->textInput() ?>

    <?= $form->field($model, 'clone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'giftcard')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
