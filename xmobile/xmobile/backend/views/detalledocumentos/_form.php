<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Detalledocumentos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalledocumentos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'DocEntry')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocNum')->textInput() ?>

    <?= $form->field($model, 'LineNum')->textInput() ?>

    <?= $form->field($model, 'BaseType')->textInput() ?>

    <?= $form->field($model, 'BaseEntry')->textInput() ?>

    <?= $form->field($model, 'BaseLine')->textInput() ?>

    <?= $form->field($model, 'LineStatus')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ItemCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Dscription')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Quantity')->textInput() ?>

    <?= $form->field($model, 'OpenQty')->textInput() ?>

    <?= $form->field($model, 'Price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DiscPrcnt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'LineTotal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'WhsCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CodeBars')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PriceAfVAT')->textInput() ?>

    <?= $form->field($model, 'TaxCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4DESCUENTO')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_4LOTE')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'GrossBase')->textInput() ?>

    <?= $form->field($model, 'idDocumento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fechaAdd')->textInput() ?>

    <?= $form->field($model, 'unidadid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idCabecera')->textInput() ?>

    <?= $form->field($model, 'idProductoPrecio')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
