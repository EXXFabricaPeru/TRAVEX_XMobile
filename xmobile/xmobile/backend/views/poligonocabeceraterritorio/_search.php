<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PoligonocabeceraterritorioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="poligonocabeceraterritorio-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fechaSistema') ?>

    <?= $form->field($model, 'fechaRegistro') ?>

    <?= $form->field($model, 'dia') ?>

    <?= $form->field($model, 'mes') ?>

    <?php // echo $form->field($model, 'idVendedor') ?>

    <?php // echo $form->field($model, 'vendedor') ?>

    <?php // echo $form->field($model, 'tipoVendedor') ?>

    <?php // echo $form->field($model, 'idTerritorio') ?>

    <?php // echo $form->field($model, 'Territorio') ?>

    <?php // echo $form->field($model, 'idPoligono') ?>

    <?php // echo $form->field($model, 'poligono') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'idUserRegister') ?>

    <?php // echo $form->field($model, 'userRegister') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
