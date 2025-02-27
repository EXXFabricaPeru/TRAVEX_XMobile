<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AlmacenesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="almacenes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Street') ?>

    <?= $form->field($model, 'WarehouseCode') ?>

    <?= $form->field($model, 'State') ?>

    <?= $form->field($model, 'Country') ?>

    <?php // echo $form->field($model, 'City') ?>

    <?php // echo $form->field($model, 'WarehouseName') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
