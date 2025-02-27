<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ListacamposusuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="listacamposusuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdcampoUsuario') ?>

    <?= $form->field($model, 'codigo') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'Status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
