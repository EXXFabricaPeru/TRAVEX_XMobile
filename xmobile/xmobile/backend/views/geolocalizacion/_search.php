<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GeolocalizacionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="geolocalizacion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idequipox') ?>

    <?= $form->field($model, 'latitud') ?>

    <?= $form->field($model, 'longitud') ?>

    <?= $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'hora') ?>

    <?php // echo $form->field($model, 'idcliente') ?>

    <?php // echo $form->field($model, 'documentocod') ?>

    <?php // echo $form->field($model, 'tipodoc') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'actividad') ?>

    <?php // echo $form->field($model, 'anexo') ?>

    <?php // echo $form->field($model, 'usuario') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'dateUpdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
