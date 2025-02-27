<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PoligonoclienteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="poligonocliente-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cardcode') ?>

    <?= $form->field($model, 'cardname') ?>

    <?= $form->field($model, 'latitud') ?>

    <?= $form->field($model, 'longitud') ?>

    <?php // echo $form->field($model, 'territoryid') ?>

    <?php // echo $form->field($model, 'territoryname') ?>

    <?php // echo $form->field($model, 'poligonoid') ?>

    <?php // echo $form->field($model, 'poligononombre') ?>

    <?php // echo $form->field($model, 'posicion') ?>

    <?php // echo $form->field($model, 'dia') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
