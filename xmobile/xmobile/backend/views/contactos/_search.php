<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ContactosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contactos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cardCode') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'direccion') ?>

    <?= $form->field($model, 'telefono1') ?>

    <?php // echo $form->field($model, 'telefono2') ?>

    <?php // echo $form->field($model, 'celular') ?>

    <?php // echo $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'comentarios') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <?php // echo $form->field($model, 'correo') ?>

    <?php // echo $form->field($model, 'titulo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
