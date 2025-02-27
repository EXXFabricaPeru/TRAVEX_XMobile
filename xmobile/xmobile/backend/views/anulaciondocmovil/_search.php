<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AnulaciondocmovilSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="anulaciondocmovil-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fechaRegistro') ?>

    <?= $form->field($model, 'usuario') ?>

    <?= $form->field($model, 'docDate') ?>

    <?= $form->field($model, 'docType') ?>

    <?php // echo $form->field($model, 'docEntry') ?>

    <?php // echo $form->field($model, 'motivoAnulacion') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'idUser') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
