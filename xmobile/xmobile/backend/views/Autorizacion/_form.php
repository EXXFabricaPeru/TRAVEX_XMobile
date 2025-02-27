<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Autorizacion-form']); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'autorizacion')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-autorizacion"></span>
        </div>
        <div class="col-md-6">
        <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\User::find()->all(), 'id', 'username'); ?>
                <?= $form->field($model, 'usuario')->dropDownList($arrx, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-usuario"></span>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
