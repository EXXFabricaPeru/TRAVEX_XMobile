<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsuarioconfiguracionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarioconfiguracion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idEstado') ?>

    <?= $form->field($model, 'idTipoPrecio') ?>

    <?= $form->field($model, 'estadoListaPrecio') ?>

    <?= $form->field($model, 'idTipoImpresora') ?>

    <?php // echo $form->field($model, 'ruta') ?>

    <?php // echo $form->field($model, 'ctaEfectivo') ?>

    <?php // echo $form->field($model, 'ctaCheque') ?>

    <?php // echo $form->field($model, 'ctaTransferencia') ?>

    <?php // echo $form->field($model, 'ctaFcEfectivo') ?>

    <?php // echo $form->field($model, 'ctaFcCheque') ?>

    <?php // echo $form->field($model, 'ctaFcTransferencia') ?>

    <?php // echo $form->field($model, 'sreOfertaVenta') ?>

    <?php // echo $form->field($model, 'sreOrdenVenta') ?>

    <?php // echo $form->field($model, 'sreFactura') ?>

    <?php // echo $form->field($model, 'sreFacturaReserva') ?>

    <?php // echo $form->field($model, 'sreCobro') ?>

    <?php // echo $form->field($model, 'flujoCaja') ?>

    <?php // echo $form->field($model, 'modInfTributaria') ?>

    <?php // echo $form->field($model, 'codEmpleadoVenta') ?>

    <?php // echo $form->field($model, 'codVendedor') ?>

    <?php // echo $form->field($model, 'nombre') ?>

    <?php // echo $form->field($model, 'almacenes') ?>

    <?php // echo $form->field($model, 'idUser') ?>

    <?php // echo $form->field($model, 'modMoneda') ?>

    <?php // echo $form->field($model, 'estadoAlmacenes') ?>

    <?php // echo $form->field($model, 'ctaTarjeta') ?>

    <?php // echo $form->field($model, 'ctaFcTarjeta') ?>

    <?php // echo $form->field($model, 'crearCliente') ?>

    <?php // echo $form->field($model, 'moneda') ?>

    <?php // echo $form->field($model, 'territorio') ?>

    <?php // echo $form->field($model, 'grupoCliente') ?>

    <?php // echo $form->field($model, 'listaPrecios') ?>

    <?php // echo $form->field($model, 'descuentos') ?>

    <?php // echo $form->field($model, 'totalDescuentoDocumento') ?>

    <?php // echo $form->field($model, 'editarDocumento') ?>

    <?php // echo $form->field($model, 'aperturaCaja') ?>

    <?php // echo $form->field($model, 'cierreCaja') ?>

    <?php // echo $form->field($model, 'totalDescuento') ?>

    <?php // echo $form->field($model, 'condicionPago') ?>

    <?php // echo $form->field($model, 'ctaanticipo') ?>

    <?php // echo $form->field($model, 'multiListaPrecios') ?>

     <?php // echo $form->field($model, 'multiCamposUsuarios') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
