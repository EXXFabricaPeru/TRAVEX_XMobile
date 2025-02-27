<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DetalledocumentosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalledocumentos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'DocEntry') ?>

    <?= $form->field($model, 'DocNum') ?>

    <?= $form->field($model, 'LineNum') ?>

    <?= $form->field($model, 'BaseType') ?>

    <?php // echo $form->field($model, 'BaseEntry') ?>

    <?php // echo $form->field($model, 'BaseLine') ?>

    <?php // echo $form->field($model, 'LineStatus') ?>

    <?php // echo $form->field($model, 'ItemCode') ?>

    <?php // echo $form->field($model, 'Dscription') ?>

    <?php // echo $form->field($model, 'Quantity') ?>

    <?php // echo $form->field($model, 'OpenQty') ?>

    <?php // echo $form->field($model, 'Price') ?>

    <?php // echo $form->field($model, 'Currency') ?>

    <?php // echo $form->field($model, 'DiscPrcnt') ?>

    <?php // echo $form->field($model, 'LineTotal') ?>

    <?php // echo $form->field($model, 'WhsCode') ?>

    <?php // echo $form->field($model, 'CodeBars') ?>

    <?php // echo $form->field($model, 'PriceAfVAT') ?>

    <?php // echo $form->field($model, 'TaxCode') ?>

    <?php // echo $form->field($model, 'U_4DESCUENTO') ?>

    <?php // echo $form->field($model, 'U_4LOTE') ?>

    <?php // echo $form->field($model, 'GrossBase') ?>

    <?php // echo $form->field($model, 'idDocumento') ?>

    <?php // echo $form->field($model, 'fechaAdd') ?>

    <?php // echo $form->field($model, 'unidadid') ?>

    <?php // echo $form->field($model, 'tc') ?>

    <?php // echo $form->field($model, 'idCabecera') ?>

    <?php // echo $form->field($model, 'idProductoPrecio') ?>

    <?php // echo $form->field($model, 'DiscMonetary') ?>

    <?php // echo $form->field($model, 'LineTotalPay') ?>

    <?php // echo $form->field($model, 'SalesUnitLength') ?>

    <?php // echo $form->field($model, 'SalesUnitWidth') ?>

    <?php // echo $form->field($model, 'SalesUnitHeight') ?>

    <?php // echo $form->field($model, 'SalesUnitVolume') ?>

    <?php // echo $form->field($model, 'DiscTotalPrcnt') ?>

    <?php // echo $form->field($model, 'DiscTotalMonetary') ?>

    <?php // echo $form->field($model, 'ICET') ?>

    <?php // echo $form->field($model, 'ICEE') ?>

    <?php // echo $form->field($model, 'ICEP') ?>

    <?php // echo $form->field($model, 'TreeType') ?>

    <?php // echo $form->field($model, 'actsl') ?>

    <?php // echo $form->field($model, 'BaseDocEntry') ?>

    <?php // echo $form->field($model, 'BaseDocLine') ?>

    <?php // echo $form->field($model, 'BaseDocType') ?>

    <?php // echo $form->field($model, 'BaseDocumentReference') ?>

    <?php // echo $form->field($model, 'GrossPrice') ?>

    <?php // echo $form->field($model, 'WarehouseCode') ?>

    <?php // echo $form->field($model, 'CorrectionInvoiceItem') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'Stock') ?>

    <?php // echo $form->field($model, 'TargetAbsEntry') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
