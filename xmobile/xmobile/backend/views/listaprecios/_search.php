<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ListapreciosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="listaprecios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'GroupNum') ?>

    <?= $form->field($model, 'BasePriceList') ?>

    <?= $form->field($model, 'PriceListNo') ?>

    <?= $form->field($model, 'PriceListName') ?>

    <?php // echo $form->field($model, 'DefaultPrimeCurrency') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
