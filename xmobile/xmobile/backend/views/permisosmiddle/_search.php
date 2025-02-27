<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PermisosmiddleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="permisosmiddle-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?= $form->field($model, 'userName') ?>

    <?= $form->field($model, 'nivel') ?>

    <?= $form->field($model, 'descripcionNivel') ?>

    <?php // echo $form->field($model, 'departamento') ?>

    <?php // echo $form->field($model, 'idCargoEmpresa') ?>

    <?php // echo $form->field($model, 'cargoEmpresa') ?>

    <?php // echo $form->field($model, 'permisomenu') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
