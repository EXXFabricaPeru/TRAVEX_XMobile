<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\XmffacturaspagosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xmffacturaspagos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idCabecera') ?>

    <?= $form->field($model, 'clienteId') ?>

    <?= $form->field($model, 'nro_recibo') ?>

    <?= $form->field($model, 'documentoId') ?>

    <?php // echo $form->field($model, 'docentry') ?>

    <?php // echo $form->field($model, 'monto') ?>

    <?php // echo $form->field($model, 'CardName') ?>

    <?php // echo $form->field($model, 'saldo') ?>

    <?php // echo $form->field($model, 'nroFactura') ?>

    <?php // echo $form->field($model, 'DocTotal') ?>

    <?php // echo $form->field($model, 'cuota') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
