<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\LogEnvioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-envio-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idlog') ?>

    <?= $form->field($model, 'proceso') ?>

    <?= $form->field($model, 'envio') ?>

    <?= $form->field($model, 'respuesta') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'documento') ?>

    <?php // echo $form->field($model, 'ultimo') ?>

    <?php // echo $form->field($model, 'endpoint') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
