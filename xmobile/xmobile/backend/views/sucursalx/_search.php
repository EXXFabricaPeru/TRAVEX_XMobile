<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SucursalxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sucursalx-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'direccion') ?>

    <?= $form->field($model, 'descripcion') ?>

    <?= $form->field($model, 'codigo') ?>

    <?php // echo $form->field($model, 'empresa') ?>

    <?php // echo $form->field($model, 'telefonouno') ?>

    <?php // echo $form->field($model, 'telefonodos') ?>

    <?php // echo $form->field($model, 'pais') ?>

    <?php // echo $form->field($model, 'ciudad') ?>

    <?php // echo $form->field($model, 'leyendauno') ?>

    <?php // echo $form->field($model, 'leyendados') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
