<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Bonificacionde1Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bonificacionde1-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Code') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'U_ID_bonificacion') ?>

    <?= $form->field($model, 'U_regla') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
