<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ConfiguracionesgeneralesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="configuracionesgenerales-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'precio') ?>

    <?= $form->field($model, 'bonificacion') ?>

    <?= $form->field($model, 'grupoproductos') ?>

    <?= $form->field($model, 'grupoclientes') ?>

    <?php // echo $form->field($model, 'docificacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
