<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsuariosincronizamovilSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuariosincronizamovil-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?= $form->field($model, 'idSucursal') ?>

    <?= $form->field($model, 'equipo') ?>

    <?php // echo $form->field($model, 'servicio') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
