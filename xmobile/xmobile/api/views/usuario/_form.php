<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Usuario */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuario-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombreUsuario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'claveUsuario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idPersona')->textInput() ?>

    <?= $form->field($model, 'estadoUsuario')->textInput() ?>

    <?= $form->field($model, 'fechaUMUsuario')->textInput() ?>

    <?= $form->field($model, 'plataformaUsuario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'plataformaPlataforma')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'plataformaEmei')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
