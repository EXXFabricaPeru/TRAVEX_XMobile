<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductospreciosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="productosprecios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ItemCode') ?>

    <?= $form->field($model, 'IdListaPrecios') ?>

    <?= $form->field($model, 'IdUnidadMedida') ?>

    <?= $form->field($model, 'Price') ?>

    <?php // echo $form->field($model, 'Currency') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
