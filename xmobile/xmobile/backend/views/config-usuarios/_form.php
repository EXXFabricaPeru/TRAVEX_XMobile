<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ConfigUsuarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-usuarios-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idEstado')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idTipoPrecio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idTipoImpresora')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ruta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ctaEfectivo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ctaCheque')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ctaTransferencia')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ctaFcEfectivo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ctaFcCheque')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ctaFcTransferencia')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sreOfertaVenta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sreOrdenVenta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sreFactura')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sreFacturaReserva')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sreCobro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flujoCaja')->textInput() ?>

    <?= $form->field($model, 'modInfTributaria')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'codEmpleadoVenta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'codVendedor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
