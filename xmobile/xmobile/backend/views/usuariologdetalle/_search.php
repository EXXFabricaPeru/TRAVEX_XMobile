<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsuariologdetalleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuariolog-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fechaIngreso') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'usuario') ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?php // echo $form->field($model, 'ipAddress') ?>

    <?php // echo $form->field($model, 'codigo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
