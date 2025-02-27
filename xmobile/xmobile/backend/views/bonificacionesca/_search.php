<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BonificacionescaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bonificacionesca-search">

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

    <?= $form->field($model, 'U_tipo') ?>

    <?= $form->field($model, 'U_cliente') ?>

    <?= $form->field($model, 'U_fecha') ?>

    <?php // echo $form->field($model, 'U_fecha_inicio') ?>

    <?php // echo $form->field($model, 'U_fecha_fin') ?>

    <?php // echo $form->field($model, 'U_estado') ?>

    <?php // echo $form->field($model, 'U_entrega') ?>

    <?php // echo $form->field($model, 'U_cantidadbonificacion') ?>

    <?php // echo $form->field($model, 'U_observacion') ?>

    <?php // echo $form->field($model, 'U_reglatipo') ?>

    <?php // echo $form->field($model, 'U_reglaunidad') ?>

    <?php // echo $form->field($model, 'U_reglacantidad') ?>

    <?php // echo $form->field($model, 'U_bonificaciontipo') ?>

    <?php // echo $form->field($model, 'U_bonificacionunidad') ?>

    <?php // echo $form->field($model, 'U_bonificacioncantidad') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
