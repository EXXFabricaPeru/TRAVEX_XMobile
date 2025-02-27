<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ViobtienedocumentosanuladosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="viobtienedocumentosanulados-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'fechaRegistro') ?>

    <?= $form->field($model, 'docDate') ?>

    <?= $form->field($model, 'docEntry') ?>

    <?= $form->field($model, 'docType') ?>

    <?= $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'docNum') ?>

    <?php // echo $form->field($model, 'motivoAnulacion') ?>

    <?php // echo $form->field($model, 'origen') ?>

    <?php // echo $form->field($model, 'usuario') ?>

    <?php // echo $form->field($model, 'idUser') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
