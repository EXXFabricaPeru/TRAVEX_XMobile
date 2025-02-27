<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Pagos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pagos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'documentoId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'clienteId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'formaPago')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipoCambioDolar')->textInput() ?>

    <?= $form->field($model, 'moneda')->textInput() ?>

    <?= $form->field($model, 'monto')->textInput() ?>

    <?= $form->field($model, 'numCheque')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numComprobante')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numTarjeta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numAhorro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numAutorizacion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bancoCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ci')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha')->textInput() ?>

    <?= $form->field($model, 'hora')->textInput() ?>

    <?= $form->field($model, 'cambio')->textInput() ?>

    <?= $form->field($model, 'monedaDolar')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
