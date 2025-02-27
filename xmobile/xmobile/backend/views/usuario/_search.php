<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsuarioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idUsuario') ?>

    <?= $form->field($model, 'nombreUsuario') ?>

    <?= $form->field($model, 'claveUsuario') ?>

    <?= $form->field($model, 'idPersona') ?>

    <?= $form->field($model, 'estadoUsuario') ?>

    <?php // echo $form->field($model, 'fechaUMUsuario') ?>

    <?php // echo $form->field($model, 'plataformaUsuario') ?>

    <?php // echo $form->field($model, 'plataformaPlataforma') ?>

    <?php // echo $form->field($model, 'plataformaEmei') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
