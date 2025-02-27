<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\XmfcabezerapagosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xmfcabezerapagos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nro_recibo') ?>

    <?= $form->field($model, 'correlativo') ?>

    <?= $form->field($model, 'usuario') ?>

    <?= $form->field($model, 'documentoId') ?>

    <?php // echo $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'hora') ?>

    <?php // echo $form->field($model, 'monto_total') ?>

    <?php // echo $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'otpp') ?>

    <?php // echo $form->field($model, 'tipo_cambio') ?>

    <?php // echo $form->field($model, 'moneda') ?>

    <?php // echo $form->field($model, 'cliente_carcode') ?>

    <?php // echo $form->field($model, 'razon_social') ?>

    <?php // echo $form->field($model, 'nit') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'cancelado') ?>

    <?php // echo $form->field($model, 'tipoTarjeta') ?>

    <?php // echo $form->field($model, 'equipo') ?>

    <?php // echo $form->field($model, 'fechaSistema') ?>

    <?php // echo $form->field($model, 'TransId') ?>

    <?php // echo $form->field($model, 'latitud') ?>

    <?php // echo $form->field($model, 'longitud') ?>

    <?php // echo $form->field($model, 'idDocumento') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
