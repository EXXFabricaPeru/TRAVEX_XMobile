<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CabeceradocumentosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cabeceradocumentos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'DocEntry') ?>

    <?= $form->field($model, 'DocNum') ?>

    <?= $form->field($model, 'DocType') ?>

    <?= $form->field($model, 'canceled') ?>

    <?= $form->field($model, 'Printed') ?>

    <?php // echo $form->field($model, 'DocStatus') ?>

    <?php // echo $form->field($model, 'DocDate') ?>

    <?php // echo $form->field($model, 'DocDueDate') ?>

    <?php // echo $form->field($model, 'CardCode') ?>

    <?php // echo $form->field($model, 'CardName') ?>

    <?php // echo $form->field($model, 'NumAtCard') ?>

    <?php // echo $form->field($model, 'DiscPrcnt') ?>

    <?php // echo $form->field($model, 'DiscSum') ?>

    <?php // echo $form->field($model, 'DocCur') ?>

    <?php // echo $form->field($model, 'DocRate') ?>

    <?php // echo $form->field($model, 'DocTotal') ?>

    <?php // echo $form->field($model, 'PaidToDate') ?>

    <?php // echo $form->field($model, 'Ref1') ?>

    <?php // echo $form->field($model, 'Ref2') ?>

    <?php // echo $form->field($model, 'Comments') ?>

    <?php // echo $form->field($model, 'JrnlMemo') ?>

    <?php // echo $form->field($model, 'GroupNum') ?>

    <?php // echo $form->field($model, 'SlpCode') ?>

    <?php // echo $form->field($model, 'Series') ?>

    <?php // echo $form->field($model, 'TaxDate') ?>

    <?php // echo $form->field($model, 'LicTradNum') ?>

    <?php // echo $form->field($model, 'Address') ?>

    <?php // echo $form->field($model, 'UserSign') ?>

    <?php // echo $form->field($model, 'CreateDate') ?>

    <?php // echo $form->field($model, 'UserSign2') ?>

    <?php // echo $form->field($model, 'UpdateDate') ?>

    <?php // echo $form->field($model, 'U_4MOTIVOCANCELADO') ?>

    <?php // echo $form->field($model, 'U_4NIT') ?>

    <?php // echo $form->field($model, 'U_4RAZON_SOCIAL') ?>

    <?php // echo $form->field($model, 'U_LATITUD') ?>

    <?php // echo $form->field($model, 'U_LONGITUD') ?>

    <?php // echo $form->field($model, 'U_4SUBTOTAL') ?>

    <?php // echo $form->field($model, 'U_4DOCUMENTOORIGEN') ?>

    <?php // echo $form->field($model, 'U_4MIGRADOCONCEPTO') ?>

    <?php // echo $form->field($model, 'U_4MIGRADO') ?>

    <?php // echo $form->field($model, 'PriceListNum') ?>

    <?php // echo $form->field($model, 'estadosend') ?>

    <?php // echo $form->field($model, 'fecharegistro') ?>

    <?php // echo $form->field($model, 'fechaupdate') ?>

    <?php // echo $form->field($model, 'fechasend') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'idDocPedido') ?>

    <?php // echo $form->field($model, 'idUser') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'gestion') ?>

    <?php // echo $form->field($model, 'mes') ?>

    <?php // echo $form->field($model, 'correlativo') ?>

    <?php // echo $form->field($model, 'rowNum') ?>

    <?php // echo $form->field($model, 'DocTotalPay') ?>

    <?php // echo $form->field($model, 'PayTermsGrpCode') ?>

    <?php // echo $form->field($model, 'TotalDiscMonetary') ?>

    <?php // echo $form->field($model, 'TotalDiscPrcnt') ?>

    <?php // echo $form->field($model, 'DocNumSAP') ?>

    <?php // echo $form->field($model, 'UNumFactura') ?>

    <?php // echo $form->field($model, 'ControlCode') ?>

    <?php // echo $form->field($model, 'actsl') ?>

    <?php // echo $form->field($model, 'Indicator') ?>

    <?php // echo $form->field($model, 'ShipToCode') ?>

    <?php // echo $form->field($model, 'ControlAccount') ?>

    <?php // echo $form->field($model, 'U_LB_NumeroFactura') ?>

    <?php // echo $form->field($model, 'U_LB_EstadoFactura') ?>

    <?php // echo $form->field($model, 'U_LB_NumeroAutorizac') ?>

    <?php // echo $form->field($model, 'U_LB_TipoFactura') ?>

    <?php // echo $form->field($model, 'U_LB_TotalNCND') ?>

    <?php // echo $form->field($model, 'Reserve') ?>

    <?php // echo $form->field($model, 'clone') ?>

    <?php // echo $form->field($model, 'giftcard') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
