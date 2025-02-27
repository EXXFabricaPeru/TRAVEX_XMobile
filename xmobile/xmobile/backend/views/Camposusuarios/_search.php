<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CamposUsuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="camposusuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'Objeto') ?>

    <?= $form->field($model, 'Nombre') ?>

    <?= $form->field($model, 'Tblsap') ?>

    <?= $form->field($model, 'Campsap') ?>

    <?php // echo $form->field($model, 'tipocampo') ?>

    <?php // echo $form->field($model, 'longitud') ?>

    <?php // echo $form->field($model, 'Fechainsert') ?>

    <?php // echo $form->field($model, 'Userinser') ?>

    <?php // echo $form->field($model, 'FechaUpdate') ?>

    <?php // echo $form->field($model, 'UserUpdate') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
