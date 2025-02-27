<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PagosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pagos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'documentoId') ?>

    <?= $form->field($model, 'clienteId') ?>

    <?= $form->field($model, 'formaPago') ?>

    <?= $form->field($model, 'tipoCambioDolar') ?>

    <?php // echo $form->field($model, 'moneda') ?>

    <?php // echo $form->field($model, 'monto') ?>

    <?php // echo $form->field($model, 'numCheque') ?>

    <?php // echo $form->field($model, 'numComprobante') ?>

    <?php // echo $form->field($model, 'numTarjeta') ?>

    <?php // echo $form->field($model, 'numAhorro') ?>

    <?php // echo $form->field($model, 'numAutorizacion') ?>

    <?php // echo $form->field($model, 'bancoCode') ?>

    <?php // echo $form->field($model, 'ci') ?>

    <?php // echo $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'hora') ?>

    <?php // echo $form->field($model, 'cambio') ?>

    <?php // echo $form->field($model, 'monedaDolar') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
