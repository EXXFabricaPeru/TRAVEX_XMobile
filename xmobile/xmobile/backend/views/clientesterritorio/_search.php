<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ClientesterritorioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clientesterritorio-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'CardCode') ?>

    <?= $form->field($model, 'CardName') ?>

    <?= $form->field($model, 'TerritoryId') ?>

    <?= $form->field($model, 'TerritoryName') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
