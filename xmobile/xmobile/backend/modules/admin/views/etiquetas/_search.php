<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\admin\models\EtiquetasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="etiquetas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'clave') ?>

    <?= $form->field($model, 'valor') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'idlocalidad') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
