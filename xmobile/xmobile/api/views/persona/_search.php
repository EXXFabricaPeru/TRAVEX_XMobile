<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PersonaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="persona-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idPersona') ?>

    <?= $form->field($model, 'nombrePersona') ?>

    <?= $form->field($model, 'apellidoPPersona') ?>

    <?= $form->field($model, 'apellidoMPersona') ?>

    <?= $form->field($model, 'estadoPersona') ?>

    <?php // echo $form->field($model, 'fechaUMPersona') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
