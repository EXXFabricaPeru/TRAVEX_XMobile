<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\XmfmediospagosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xmfmediospagos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idCabecera') ?>

    <?= $form->field($model, 'nro_recibo') ?>

    <?= $form->field($model, 'documentoId') ?>

    <?= $form->field($model, 'formaPago') ?>

    <?php // echo $form->field($model, 'monto') ?>

    <?php // echo $form->field($model, 'numCheque') ?>

    <?php // echo $form->field($model, 'numComprobante') ?>

    <?php // echo $form->field($model, 'numTarjeta') ?>

    <?php // echo $form->field($model, 'bancoCode') ?>

    <?php // echo $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'cambio') ?>

    <?php // echo $form->field($model, 'monedaDolar') ?>

    <?php // echo $form->field($model, 'monedaLocal') ?>

    <?php // echo $form->field($model, 'centro') ?>

    <?php // echo $form->field($model, 'baucher') ?>

    <?php // echo $form->field($model, 'checkdate') ?>

    <?php // echo $form->field($model, 'transferencedate') ?>

    <?php // echo $form->field($model, 'CreditCard') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
