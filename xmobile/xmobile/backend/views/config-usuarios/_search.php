<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ConfigUsuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-usuarios-search">

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

    <?= $form->field($model, 'idTipoImpresora') ?>

    <?= $form->field($model, 'ruta') ?>

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

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
