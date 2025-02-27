<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ConfiguracionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="configuracion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'parametro') ?>

    <?= $form->field($model, 'valor') ?>

    <?= $form->field($model, 'especificacion') ?>

    <?= $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'valor2') ?>

    <?php // echo $form->field($model, 'valor3') ?>

    <?php // echo $form->field($model, 'valor4') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
