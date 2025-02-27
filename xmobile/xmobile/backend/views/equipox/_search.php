<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipoxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipox-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'equipo') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'keyid') ?>

    <?= $form->field($model, 'plataforma') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'registrado') ?>

    <?php // echo $form->field($model, 'version') ?>

    <?php // echo $form->field($model, 'sucursalxId') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
