<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="productos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ItemCode') ?>

    <?= $form->field($model, 'ItemName') ?>

    <?= $form->field($model, 'ItemsGroupCode') ?>

    <?= $form->field($model, 'ForeignName') ?>

    <?php // echo $form->field($model, 'CustomsGroupCode') ?>

    <?php // echo $form->field($model, 'BarCode') ?>

    <?php // echo $form->field($model, 'PurchaseItem') ?>

    <?php // echo $form->field($model, 'SalesItem') ?>

    <?php // echo $form->field($model, 'InventoryItem') ?>

    <?php // echo $form->field($model, 'UserText') ?>

    <?php // echo $form->field($model, 'SerialNum') ?>

    <?php // echo $form->field($model, 'QuantityOnStock') ?>

    <?php // echo $form->field($model, 'QuantityOrderedFromVendors') ?>

    <?php // echo $form->field($model, 'QuantityOrderedByCustomers') ?>

    <?php // echo $form->field($model, 'ManageSerialNumbers') ?>

    <?php // echo $form->field($model, 'ManageBatchNumbers') ?>

    <?php // echo $form->field($model, 'SalesUnit') ?>

    <?php // echo $form->field($model, 'SalesUnitLength') ?>

    <?php // echo $form->field($model, 'SalesUnitWidth') ?>

    <?php // echo $form->field($model, 'SalesUnitHeight') ?>

    <?php // echo $form->field($model, 'SalesUnitVolume') ?>

    <?php // echo $form->field($model, 'PurchaseUnit') ?>

    <?php // echo $form->field($model, 'DefaultWarehouse') ?>

    <?php // echo $form->field($model, 'ManageStockByWarehouse') ?>

    <?php // echo $form->field($model, 'ForceSelectionOfSerialNumber') ?>

    <?php // echo $form->field($model, 'Series') ?>

    <?php // echo $form->field($model, 'UoMGroupEntry') ?>

    <?php // echo $form->field($model, 'DefaultSalesUoMEntry') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <?php // echo $form->field($model, 'Manufacturer') ?>

    <?php // echo $form->field($model, 'NoDiscounts') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'combo') ?>

    <?php // echo $form->field($model, 'producto_std1') ?>

    <?php // echo $form->field($model, 'producto_std2') ?>

    <?php // echo $form->field($model, 'producto_std3') ?>

    <?php // echo $form->field($model, 'producto_std4') ?>

    <?php // echo $form->field($model, 'producto_std5') ?>

    <?php // echo $form->field($model, 'producto_std6') ?>

    <?php // echo $form->field($model, 'producto_std7') ?>

    <?php // echo $form->field($model, 'producto_std8') ?>

    <?php // echo $form->field($model, 'producto_std9') ?>

    <?php // echo $form->field($model, 'producto_std10') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
