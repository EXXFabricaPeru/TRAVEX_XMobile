<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NumeracionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="numeracion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'numcli') ?>

    <?= $form->field($model, 'numdof') ?>

    <?= $form->field($model, 'numdoe') ?>

    <?= $form->field($model, 'numdfa') ?>

    <?php // echo $form->field($model, 'numdop') ?>

    <?php // echo $form->field($model, 'numgp') ?>

    <?php // echo $form->field($model, 'numgpa') ?>

    <?php // echo $form->field($model, 'numccaja') ?>

    <?php // echo $form->field($model, 'iduser') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
