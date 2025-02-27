<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipoxcuentascontablesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipoxcuentascontables-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'equipoxId') ?>

    <?= $form->field($model, 'cuentaEfectivo') ?>

    <?= $form->field($model, 'cuentaCheque') ?>

    <?= $form->field($model, 'cuentaTranferencia') ?>

    <?php // echo $form->field($model, 'cuentaTarjeta') ?>

    <?php // echo $form->field($model, 'cuentaAnticipos') ?>

    <?php // echo $form->field($model, 'cuentaEfectivoUSD') ?>

    <?php // echo $form->field($model, 'cuentaChequeUSD') ?>

    <?php // echo $form->field($model, 'cuentaTranferenciaUSD') ?>

    <?php // echo $form->field($model, 'cuentaTarjetaUSD') ?>

    <?php // echo $form->field($model, 'cuentaAnticiposUSD') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
