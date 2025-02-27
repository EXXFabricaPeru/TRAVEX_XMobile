<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CuentascontablesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cuentascontables-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Code') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'Balance') ?>

    <?= $form->field($model, 'AccountLevel') ?>

    <?php // echo $form->field($model, 'FatherAccountKey') ?>

    <?php // echo $form->field($model, 'AcctCurrency') ?>

    <?php // echo $form->field($model, 'FormatCode') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
