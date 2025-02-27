<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\LbccSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lbcc-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Code') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'DocEntry') ?>

    <?= $form->field($model, 'Canceled') ?>

    <?php // echo $form->field($model, 'Object') ?>

    <?php // echo $form->field($model, 'LogInst') ?>

    <?php // echo $form->field($model, 'UserSign') ?>

    <?php // echo $form->field($model, 'Transfered') ?>

    <?php // echo $form->field($model, 'CreateDate') ?>

    <?php // echo $form->field($model, 'CreateTime') ?>

    <?php // echo $form->field($model, 'UpdateDate') ?>

    <?php // echo $form->field($model, 'UpdateTime') ?>

    <?php // echo $form->field($model, 'DataSource') ?>

    <?php // echo $form->field($model, 'U_NumeroAutorizacion') ?>

    <?php // echo $form->field($model, 'U_ObjType') ?>

    <?php // echo $form->field($model, 'U_Estado') ?>

    <?php // echo $form->field($model, 'U_PrimerNumero') ?>

    <?php // echo $form->field($model, 'U_NumeroSiguiente') ?>

    <?php // echo $form->field($model, 'U_UltimoNumero') ?>

    <?php // echo $form->field($model, 'U_Series') ?>

    <?php // echo $form->field($model, 'U_SeriesName') ?>

    <?php // echo $form->field($model, 'U_FechaLimiteEmision') ?>

    <?php // echo $form->field($model, 'U_LlaveDosificacion') ?>

    <?php // echo $form->field($model, 'U_Leyenda') ?>

    <?php // echo $form->field($model, 'U_Leyenda2') ?>

    <?php // echo $form->field($model, 'U_TipoDosificacion') ?>

    <?php // echo $form->field($model, 'U_Sucursal') ?>

    <?php // echo $form->field($model, 'U_EmpleadoVentas') ?>

    <?php // echo $form->field($model, 'U_GrupoCliente') ?>

    <?php // echo $form->field($model, 'U_Actividad') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <?php // echo $form->field($model, 'equipoId') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
